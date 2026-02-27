<?php

/**
 * Cuentas por Pagar (Accounts Payable Aging) Controller.
 *
 * Returns expenses grouped by vendor that have NOT been paid yet,
 * with aging classification (green/yellow/red) based on days since creation.
 */

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CuentasPorPagarController extends BaseController
{
    /**
     * GET /api/v1/cuentas-por-pagar
     *
     * Returns all unpaid expenses grouped by vendor with aging info.
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
            ->with(['vendor', 'project'])
            ->orderBy('date', 'asc')
            ->get();

        $vendorGroups = [];
        $grandTotal = 0.0;
        $today = now();

        foreach ($expenses as $expense) {
            $vendorId = $expense->vendor_id ?? 0;
            $vendorName = $expense->vendor ? $expense->vendor->name : 'Sin Proveedor';

            if (!isset($vendorGroups[$vendorId])) {
                $vendorGroups[$vendorId] = [
                    'vendor_id' => $vendorId,
                    'vendor_name' => $vendorName,
                    'total' => 0.0,
                    'count' => 0,
                    'expenses' => [],
                ];
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

        // Round vendor totals
        foreach ($vendorGroups as &$group) {
            $group['total'] = round($group['total'], 2);
        }

        // Summary counts
        $allExpenses = collect($vendorGroups)->pluck('expenses')->flatten(1);
        $summary = [
            'grand_total' => round($grandTotal, 2),
            'total_count' => $allExpenses->count(),
            'green_count' => $allExpenses->where('status', 'green')->count(),
            'yellow_count' => $allExpenses->where('status', 'yellow')->count(),
            'red_count' => $allExpenses->where('status', 'red')->count(),
            'green_total' => round($allExpenses->where('status', 'green')->sum('amount'), 2),
            'yellow_total' => round($allExpenses->where('status', 'yellow')->sum('amount'), 2),
            'red_total' => round($allExpenses->where('status', 'red')->sum('amount'), 2),
        ];

        return response()->json([
            'data' => array_values($vendorGroups),
            'summary' => $summary,
        ]);
    }
}
