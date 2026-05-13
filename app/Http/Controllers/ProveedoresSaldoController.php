<?php

/**
 * Proveedores - Saldo Global Controller.
 *
 * Returns the total balance owed to each vendor, aggregating across all houses/projects.
 * For each vendor returns:
 *   - total_contratado: sum of all active Purchase Order amounts
 *   - total_facturado:  sum of all expenses (invoices received)
 *   - total_pagado:     sum of expenses already paid
 *   - saldo_pendiente:  total still owed (unpaid expenses + uninvoiced contract balance)
 *   - desglose por proyecto/casa
 */

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProveedoresSaldoController extends BaseController
{
    /**
     * GET /api/v1/proveedores-saldo
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $vendors = Vendor::where('company_id', $company->id)
            ->where('is_deleted', 0)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $vendorIds = $vendors->pluck('id')->all();

        $expenses = Expense::where('company_id', $company->id)
            ->whereIn('vendor_id', $vendorIds)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->with('project')
            ->get();

        $purchaseOrders = PurchaseOrder::where('company_id', $company->id)
            ->whereIn('vendor_id', $vendorIds)
            ->where('is_deleted', 0)
            ->whereIn('status_id', [
                PurchaseOrder::STATUS_SENT,
                PurchaseOrder::STATUS_ACCEPTED,
                PurchaseOrder::STATUS_RECEIVED,
            ])
            ->with('project')
            ->get();

        $expensesByVendor = $expenses->groupBy('vendor_id');
        $posByVendor = $purchaseOrders->groupBy('vendor_id');

        $rows = [];
        $totals = [
            'total_contratado' => 0.0,
            'total_facturado' => 0.0,
            'total_pagado' => 0.0,
            'saldo_pendiente' => 0.0,
        ];

        foreach ($vendors as $vendor) {
            $vendorExpenses = $expensesByVendor->get($vendor->id, collect());
            $vendorPOs = $posByVendor->get($vendor->id, collect());

            $byProject = [];

            // Aggregate expenses by project
            foreach ($vendorExpenses as $expense) {
                $projectId = $expense->project_id ?? 0;
                $projectName = $expense->project ? $expense->project->name : 'Sin Proyecto';

                if (!isset($byProject[$projectId])) {
                    $byProject[$projectId] = $this->makeProjectRow($projectId, $projectName);
                }

                $amount = (float) $expense->amount;
                $isPaid = !empty($expense->payment_date);

                $byProject[$projectId]['facturado'] += $amount;
                $byProject[$projectId]['expense_count']++;

                if ($isPaid) {
                    $byProject[$projectId]['pagado'] += $amount;
                } else {
                    $byProject[$projectId]['pendiente_factura'] += $amount;
                }
            }

            // Aggregate POs by project (contract amounts + remaining balance)
            foreach ($vendorPOs as $po) {
                $projectId = $po->project_id ?? 0;
                $projectName = $po->project ? $po->project->name : 'Sin Proyecto';

                if (!isset($byProject[$projectId])) {
                    $byProject[$projectId] = $this->makeProjectRow($projectId, $projectName);
                }

                $poAmount = (float) $po->amount;
                $poBalance = (float) $po->balance;

                // Uncommitted balance = PO balance minus unpaid expenses already linked to this PO
                $unpaidLinkedExpenses = $vendorExpenses
                    ->where('purchase_order_id', $po->id)
                    ->filter(fn ($e) => empty($e->payment_date))
                    ->sum('amount');
                $saldoContrato = max(0, $poBalance - $unpaidLinkedExpenses);

                $byProject[$projectId]['contratado'] += $poAmount;
                $byProject[$projectId]['po_count']++;
                $byProject[$projectId]['saldo_contrato'] += $saldoContrato;
            }

            // Compute project-level saldo_pendiente
            $vendorTotals = [
                'contratado' => 0.0,
                'facturado' => 0.0,
                'pagado' => 0.0,
                'pendiente_factura' => 0.0,
                'saldo_contrato' => 0.0,
                'saldo_pendiente' => 0.0,
            ];

            foreach ($byProject as &$row) {
                $row['saldo_pendiente'] = round(
                    $row['pendiente_factura'] + $row['saldo_contrato'],
                    2
                );
                $row['contratado'] = round($row['contratado'], 2);
                $row['facturado'] = round($row['facturado'], 2);
                $row['pagado'] = round($row['pagado'], 2);
                $row['pendiente_factura'] = round($row['pendiente_factura'], 2);
                $row['saldo_contrato'] = round($row['saldo_contrato'], 2);

                $vendorTotals['contratado'] += $row['contratado'];
                $vendorTotals['facturado'] += $row['facturado'];
                $vendorTotals['pagado'] += $row['pagado'];
                $vendorTotals['pendiente_factura'] += $row['pendiente_factura'];
                $vendorTotals['saldo_contrato'] += $row['saldo_contrato'];
                $vendorTotals['saldo_pendiente'] += $row['saldo_pendiente'];
            }
            unset($row);

            // Skip vendors with no activity at all
            if ($vendorTotals['contratado'] == 0 && $vendorTotals['facturado'] == 0) {
                continue;
            }

            $rows[] = [
                'vendor_id' => $vendor->hashed_id,
                'vendor_name' => $vendor->name,
                'total_contratado' => round($vendorTotals['contratado'], 2),
                'total_facturado' => round($vendorTotals['facturado'], 2),
                'total_pagado' => round($vendorTotals['pagado'], 2),
                'total_pendiente_factura' => round($vendorTotals['pendiente_factura'], 2),
                'total_saldo_contrato' => round($vendorTotals['saldo_contrato'], 2),
                'saldo_pendiente' => round($vendorTotals['saldo_pendiente'], 2),
                'project_count' => count($byProject),
                'by_project' => array_values($byProject),
            ];

            $totals['total_contratado'] += $vendorTotals['contratado'];
            $totals['total_facturado'] += $vendorTotals['facturado'];
            $totals['total_pagado'] += $vendorTotals['pagado'];
            $totals['saldo_pendiente'] += $vendorTotals['saldo_pendiente'];
        }

        // Sort vendors by saldo_pendiente descending (most owed first)
        usort($rows, fn ($a, $b) => $b['saldo_pendiente'] <=> $a['saldo_pendiente']);

        $summary = [
            'vendor_count' => count($rows),
            'total_contratado' => round($totals['total_contratado'], 2),
            'total_facturado' => round($totals['total_facturado'], 2),
            'total_pagado' => round($totals['total_pagado'], 2),
            'saldo_pendiente' => round($totals['saldo_pendiente'], 2),
        ];

        return response()->json([
            'data' => $rows,
            'summary' => $summary,
        ]);
    }

    private function makeProjectRow(int $projectId, string $projectName): array
    {
        return [
            'project_id' => $projectId,
            'project_name' => $projectName,
            'contratado' => 0.0,
            'facturado' => 0.0,
            'pagado' => 0.0,
            'pendiente_factura' => 0.0,
            'saldo_contrato' => 0.0,
            'saldo_pendiente' => 0.0,
            'po_count' => 0,
            'expense_count' => 0,
        ];
    }
}
