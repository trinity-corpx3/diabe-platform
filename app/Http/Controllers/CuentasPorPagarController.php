<?php

/**
 * Cuentas por Pagar (Accounts Payable Aging) Controller.
 *
 * Returns expenses grouped by vendor that have NOT been paid yet,
 * with aging classification (green/yellow/red) based on days since creation.
 *
 * PO balances without associated unpaid expenses are returned in a separate
 * `saldo_contrato` bucket per vendor (they represent committed contract amounts
 * not yet invoiced, not actual overdue debt).
 */

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CuentasPorPagarController extends BaseController
{
    /**
     * GET /api/v1/cuentas-por-pagar
     *
     * Returns all unpaid expenses grouped by vendor with aging info,
     * plus contract balance (saldo de OC) as a separate bucket.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        // Fetch all unpaid expenses (no payment_date) that are not deleted
        $expenses = Expense::where('company_id', $company->id)
            ->whereNull('payment_date')
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->with(['vendor', 'project'])
            ->orderBy('date', 'asc')
            ->get();

        // Fetch all Purchase Orders with balance > 0
        $purchaseOrders = PurchaseOrder::where('company_id', $company->id)
            ->where('balance', '>', 0)
            ->where('is_deleted', 0)
            ->whereIn('status_id', [PurchaseOrder::STATUS_SENT, PurchaseOrder::STATUS_ACCEPTED, PurchaseOrder::STATUS_RECEIVED])
            ->with(['vendor', 'project'])
            ->get();

        $vendorGroups = [];
        $grandTotal = 0.0;
        $saldoContratoGrandTotal = 0.0;
        $today = now();

        foreach ($expenses as $expense) {
            $vendorId = $expense->vendor_id ?? 0;
            $vendorName = $expense->vendor ? $expense->vendor->name : 'Sin Proveedor';

            if (!isset($vendorGroups[$vendorId])) {
                $vendorGroups[$vendorId] = $this->makeVendorGroup($vendorId, $vendorName);
            }

            $expenseDate = $expense->date ? \Carbon\Carbon::parse($expense->date) : $today;
            $daysAging = (int) $expenseDate->diffInDays($today);

            // Traffic light classification
            if ($daysAging <= 15) {
                $status = 'green';
                $statusLabel = 'Al corriente (≤15 días)';
            } elseif ($daysAging <= 30) {
                $status = 'yellow';
                $statusLabel = 'Próximo a vencer (16-30 días)';
            } else {
                $status = 'red';
                $statusLabel = 'Vencido (>30 días)';
            }

            $amount = (float) $expense->amount;
            $projectName = $expense->project ? $expense->project->name : 'Sin Proyecto';

            $vendorGroups[$vendorId]['total'] += $amount;
            $vendorGroups[$vendorId]['count']++;
            $vendorGroups[$vendorId]['expenses'][] = [
                'id' => $expense->id,
                'number' => $expense->number ?? '',
                'date' => $expense->date,
                'amount' => round($amount, 2),
                'project_name' => $projectName,
                'project_id' => $expense->project_id,
                'days_aging' => $daysAging,
                'status' => $status,
                'status_label' => $statusLabel,
                'notes' => $expense->public_notes ?? '',
                'category' => $expense->category ? $expense->category->name : '',
            ];

            $grandTotal += $amount;
        }

        // Process Purchase Orders — remaining contract balance (not yet invoiced)
        // goes into a separate `saldo_contrato` bucket per vendor.
        foreach ($purchaseOrders as $po) {
            $vendorId = $po->vendor_id ?? 0;
            $vendorName = $po->vendor ? $po->vendor->name : 'Sin Proveedor';

            // Subtract unpaid expenses already counted to avoid double counting
            $unpaidExpensesForPO = $expenses->where('purchase_order_id', $po->id)->sum('amount');
            $remainingPOBalance = max(0, (float) $po->balance - $unpaidExpensesForPO);

            if ($remainingPOBalance <= 0) {
                continue;
            }

            if (!isset($vendorGroups[$vendorId])) {
                $vendorGroups[$vendorId] = $this->makeVendorGroup($vendorId, $vendorName);
            }

            $projectName = $po->project ? $po->project->name : 'Sin Proyecto';
            $poDate = $po->date ? \Carbon\Carbon::parse($po->date) : $today;
            $daysSincePO = (int) $poDate->diffInDays($today);

            $vendorGroups[$vendorId]['saldo_contrato_total'] += $remainingPOBalance;
            $vendorGroups[$vendorId]['saldo_contrato_count']++;
            $vendorGroups[$vendorId]['saldo_contrato'][] = [
                'id' => 'po_' . $po->id,
                'po_id' => $po->hashed_id,
                'number' => $po->number ?? '',
                'date' => $po->date,
                'amount' => round($remainingPOBalance, 2),
                'po_amount' => round((float) $po->amount, 2),
                'po_paid_to_date' => round((float) $po->paid_to_date, 2),
                'project_name' => $projectName,
                'project_id' => $po->project_id,
                'days_since_po' => $daysSincePO,
                'notes' => $po->public_notes ?? '',
            ];

            // Vendor total includes both aged expenses and contract balance
            $vendorGroups[$vendorId]['total'] += $remainingPOBalance;
            $grandTotal += $remainingPOBalance;
            $saldoContratoGrandTotal += $remainingPOBalance;
        }

        // Round vendor totals
        foreach ($vendorGroups as &$group) {
            $group['total'] = round($group['total'], 2);
            $group['saldo_contrato_total'] = round($group['saldo_contrato_total'], 2);
        }
        unset($group);

        // Summary counts — aging refers only to actual unpaid expenses
        $allExpenses = collect($vendorGroups)->pluck('expenses')->flatten(1);
        $saldoContratoCount = collect($vendorGroups)->sum('saldo_contrato_count');

        $summary = [
            'grand_total' => round($grandTotal, 2),
            'total_count' => $allExpenses->count(),
            'green_count' => $allExpenses->where('status', 'green')->count(),
            'yellow_count' => $allExpenses->where('status', 'yellow')->count(),
            'red_count' => $allExpenses->where('status', 'red')->count(),
            'green_total' => round($allExpenses->where('status', 'green')->sum('amount'), 2),
            'yellow_total' => round($allExpenses->where('status', 'yellow')->sum('amount'), 2),
            'red_total' => round($allExpenses->where('status', 'red')->sum('amount'), 2),
            'saldo_contrato_total' => round($saldoContratoGrandTotal, 2),
            'saldo_contrato_count' => $saldoContratoCount,
        ];

        return response()->json([
            'data' => array_values($vendorGroups),
            'summary' => $summary,
        ]);
    }

    private function makeVendorGroup(int $vendorId, string $vendorName): array
    {
        return [
            'vendor_id' => $vendorId,
            'vendor_name' => $vendorName,
            'total' => 0.0,
            'count' => 0,
            'expenses' => [],
            'saldo_contrato_total' => 0.0,
            'saldo_contrato_count' => 0,
            'saldo_contrato' => [],
        ];
    }
}
