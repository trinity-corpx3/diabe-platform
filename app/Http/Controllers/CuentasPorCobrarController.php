<?php

/**
 * Cuentas por Cobrar (Accounts Receivable Aging) Controller.
 *
 * Returns invoices grouped by client that have NOT been fully paid yet,
 * with aging classification (green/yellow/red) based on due date.
 */

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CuentasPorCobrarController extends BaseController
{
    /**
     * GET /api/v1/cuentas-por-cobrar
     *
     * Returns all unpaid invoices grouped by client with aging info.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        // Fetch all unpaid invoices (balance > 0) that are not deleted
        $invoices = Invoice::where('company_id', $company->id)
            ->where('balance', '>', 0)
            ->where('status_id', '!=', Invoice::STATUS_PAID)
            ->whereNull('deleted_at')
            ->where('is_deleted', false)
            ->with(['client', 'project'])
            ->orderBy('due_date', 'asc')
            ->get();

        $clientGroups = [];
        $grandTotal = 0.0;
        $today = now();

        foreach ($invoices as $invoice) {
            $clientId = $invoice->client_id;
            $clientName = $invoice->client ? $invoice->client->name : 'Sin Cliente';

            if (!isset($clientGroups[$clientId])) {
                $clientGroups[$clientId] = [
                    'client_id' => $clientId,
                    'client_name' => $clientName,
                    'total' => 0.0,
                    'count' => 0,
                    'invoices' => [],
                ];
            }

            // Aging logic: use due_date, fallback to date + 30 days
            $dueDate = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date) : ($invoice->date ? \Carbon\Carbon::parse($invoice->date)->addDays(30) : $today);

            // Classification
            if ($today->lte($dueDate)) {
                $status = 'green';
                $statusLabel = 'Al corriente';
                $daysAgingDisplay = 0;
            } else {
                $daysOverdue = (int) $today->diffInDays($dueDate);
                if ($daysOverdue <= 15) {
                    $status = 'yellow';
                    $statusLabel = 'Atraso leve (≤15 días)';
                } else {
                    $status = 'red';
                    $statusLabel = 'Vencido (>15 días)';
                }
                $daysAgingDisplay = $daysOverdue;
            }

            $amount = (float) $invoice->balance;
            $projectName = $invoice->project ? $invoice->project->name : 'Sin Proyecto';

            $clientGroups[$clientId]['total'] += $amount;
            $clientGroups[$clientId]['count']++;
            $clientGroups[$clientId]['invoices'][] = [
                'id' => $invoice->id,
                'number' => $invoice->number ?? '',
                'date' => $invoice->date,
                'due_date' => $invoice->due_date,
                'amount' => round($amount, 2),
                'total_amount' => round((float) $invoice->amount, 2),
                'project_name' => $projectName,
                'project_id' => $invoice->project_id,
                'days_aging' => $daysAgingDisplay,
                'status' => $status,
                'status_label' => $statusLabel,
                'notes' => $invoice->public_notes ?? '',
            ];

            $grandTotal += $amount;
        }

        // Round totals
        foreach ($clientGroups as &$group) {
            $group['total'] = round($group['total'], 2);
        }

        $allInvoices = collect($clientGroups)->pluck('invoices')->flatten(1);
        $summary = [
            'grand_total' => round($grandTotal, 2),
            'total_count' => $allInvoices->count(),
            'green_count' => $allInvoices->where('status', 'green')->count(),
            'yellow_count' => $allInvoices->where('status', 'yellow')->count(),
            'red_count' => $allInvoices->where('status', 'red')->count(),
            'green_total' => round($allInvoices->where('status', 'green')->sum('amount'), 2),
            'yellow_total' => round($allInvoices->where('status', 'yellow')->sum('amount'), 2),
            'red_total' => round($allInvoices->where('status', 'red')->sum('amount'), 2),
        ];

        return response()->json([
            'data' => array_values($clientGroups),
            'summary' => $summary,
        ]);
    }
}
