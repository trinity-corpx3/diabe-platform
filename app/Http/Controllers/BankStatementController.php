<?php

namespace App\Http\Controllers;

use App\Models\BankEntry;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Transformers\BankEntryTransformer;
use App\Utils\Traits\MakesHash;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BankStatementController extends BaseController
{
    use MakesHash;

    protected $entity_type = BankEntry::class;
    protected $entity_transformer = BankEntryTransformer::class;

    /**
     * Aggregated bank statement: merges payments (deposits), expenses (withdrawals),
     * and manual bank_entries into a single chronological ledger with IVA breakdown.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Company $company */
        $company = auth()->user()->company();
        $companyId = $company->id;

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        $projectId = $request->get('project_id');

        $decodedProjectId = $projectId ? $this->decodePrimaryKey($projectId) : null;

        $transactions = collect();

        // --- Deposits from client payments ---
        $paymentsQuery = Payment::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereIn('status_id', [Payment::STATUS_COMPLETED, Payment::STATUS_PARTIALLY_REFUNDED])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($decodedProjectId) {
            $paymentsQuery->where('project_id', $decodedProjectId);
        }

        foreach ($paymentsQuery->with(['invoices', 'client', 'project'])->get() as $payment) {
            $ivaCobrado = $this->calcPaymentIva($payment);

            $transactions->push([
                'date' => $payment->date,
                'source' => 'payment',
                'source_id' => $this->encodePrimaryKey($payment->id),
                'description' => $payment->number ? "Pago {$payment->number}" : 'Pago recibido',
                'detail' => $payment->transaction_reference ?: ($payment->private_notes ?: ''),
                'project_name' => $payment->project?->name ?? '',
                'project_id' => $payment->project_id ? $this->encodePrimaryKey($payment->project_id) : null,
                'vendor_name' => $payment->client?->present()->name() ?? '',
                'deposit' => (float) $payment->amount,
                'withdrawal' => 0.0,
                'iva_cobrado' => round($ivaCobrado, 2),
                'iva_acreditado' => 0.0,
                'balance' => 0,
                'manual_entry_id' => null,
            ]);
        }

        // --- Withdrawals from paid expenses ---
        $expensesQuery = Expense::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        if ($decodedProjectId) {
            $expensesQuery->where('project_id', $decodedProjectId);
        }

        foreach ($expensesQuery->with(['vendor', 'project'])->get() as $expense) {
            $ivaAcreditado = $expense->getTaxAmount();
            $totalAmount = $expense->uses_inclusive_taxes
                ? $expense->amount
                : $expense->amount + $ivaAcreditado;

            $transactions->push([
                'date' => $expense->payment_date,
                'source' => 'expense',
                'source_id' => $this->encodePrimaryKey($expense->id),
                'description' => $expense->number ? "Gasto {$expense->number}" : 'Gasto',
                'detail' => $expense->public_notes ?: ($expense->private_notes ?: ''),
                'project_name' => $expense->project?->name ?? '',
                'project_id' => $expense->project_id ? $this->encodePrimaryKey($expense->project_id) : null,
                'vendor_name' => $expense->vendor?->name ?? '',
                'deposit' => 0.0,
                'withdrawal' => round($totalAmount, 2),
                'iva_cobrado' => 0.0,
                'iva_acreditado' => round($ivaAcreditado, 2),
                'balance' => 0,
                'manual_entry_id' => null,
            ]);
        }

        // --- Manual bank entries ---
        $manualQuery = BankEntry::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($decodedProjectId) {
            $manualQuery->where('project_id', $decodedProjectId);
        }

        foreach ($manualQuery->with('project')->get() as $entry) {
            $isDeposit = $entry->type === BankEntry::TYPE_DEPOSIT;

            $transactions->push([
                'date' => $entry->date->format('Y-m-d'),
                'source' => 'manual',
                'source_id' => $this->encodePrimaryKey($entry->id),
                'description' => $entry->description ?: ($entry->category ?: 'Movimiento manual'),
                'detail' => $entry->reference ?: '',
                'project_name' => $entry->project?->name ?? '',
                'project_id' => $entry->project_id ? $this->encodePrimaryKey($entry->project_id) : null,
                'vendor_name' => '',
                'deposit' => $isDeposit ? (float) $entry->amount : 0.0,
                'withdrawal' => $isDeposit ? 0.0 : (float) $entry->amount,
                'iva_cobrado' => $isDeposit ? (float) $entry->iva_amount : 0.0,
                'iva_acreditado' => $isDeposit ? 0.0 : (float) $entry->iva_amount,
                'balance' => 0,
                'manual_entry_id' => (string) $this->encodePrimaryKey($entry->id),
            ]);
        }

        // Sort by date, then deposits before withdrawals
        $transactions = $transactions->sortBy([
            ['date', 'asc'],
            ['deposit', 'desc'],
        ])->values();

        // Calculate opening balance (all transactions before date_from)
        $openingBalance = $this->calcOpeningBalance($companyId, $dateFrom, $decodedProjectId);

        // Calculate running balance
        $runningBalance = $openingBalance;
        $transactions = $transactions->map(function ($t) use (&$runningBalance) {
            $runningBalance += $t['deposit'] - $t['withdrawal'];
            $t['balance'] = round($runningBalance, 2);
            return $t;
        });

        // Summary
        $totalDeposits = $transactions->sum('deposit');
        $totalWithdrawals = $transactions->sum('withdrawal');
        $ivaCobrado = $transactions->sum('iva_cobrado');
        $ivaAcreditado = $transactions->sum('iva_acreditado');

        return response()->json([
            'data' => [
                'transactions' => $transactions->values()->all(),
                'summary' => [
                    'opening_balance' => round($openingBalance, 2),
                    'total_deposits' => round($totalDeposits, 2),
                    'total_withdrawals' => round($totalWithdrawals, 2),
                    'closing_balance' => round($openingBalance + $totalDeposits - $totalWithdrawals, 2),
                    'iva_cobrado' => round($ivaCobrado, 2),
                    'iva_acreditado' => round($ivaAcreditado, 2),
                    'iva_por_pagar' => round($ivaCobrado - $ivaAcreditado, 2),
                ],
            ],
        ]);
    }

    /**
     * Derive IVA from a payment by looking at the related invoice's tax rate.
     */
    private function calcPaymentIva(Payment $payment): float
    {
        $invoices = $payment->invoices;

        if ($invoices->isEmpty()) {
            return 0.0;
        }

        $weightedIva = 0.0;
        $totalApplied = 0.0;

        foreach ($invoices as $invoice) {
            $pivot = $invoice->pivot;
            if ($pivot->deleted_at) {
                continue;
            }

            $appliedAmount = (float) $pivot->amount;
            $taxRate = (float) ($invoice->tax_rate1 ?? 0);

            if ($taxRate > 0 && $appliedAmount > 0) {
                $iva = $appliedAmount * $taxRate / (100 + $taxRate);
                $weightedIva += $iva;
            }

            $totalApplied += $appliedAmount;
        }

        if ($totalApplied > 0 && $totalApplied < $payment->amount && $weightedIva > 0) {
            $ratio = $payment->amount / $totalApplied;
            $weightedIva *= $ratio;
        }

        return $weightedIva;
    }

    /**
     * Calculate the sum of all deposits minus withdrawals before a given date.
     */
    private function calcOpeningBalance(int $companyId, string $dateFrom, ?int $projectId = null): float
    {
        $balance = 0.0;

        // Payments before period
        $pq = Payment::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereIn('status_id', [Payment::STATUS_COMPLETED, Payment::STATUS_PARTIALLY_REFUNDED])
            ->where('date', '<', $dateFrom);
        if ($projectId) {
            $pq->where('project_id', $projectId);
        }
        $balance += (float) $pq->sum('amount');

        // Expenses before period
        $eq = Expense::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereNotNull('payment_date')
            ->where('payment_date', '<', $dateFrom);
        if ($projectId) {
            $eq->where('project_id', $projectId);
        }

        foreach ($eq->get() as $expense) {
            $iva = $expense->getTaxAmount();
            $total = $expense->uses_inclusive_taxes ? $expense->amount : $expense->amount + $iva;
            $balance -= $total;
        }

        // Manual entries before period
        $mq = BankEntry::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->where('date', '<', $dateFrom);
        if ($projectId) {
            $mq->where('project_id', $projectId);
        }

        foreach ($mq->get() as $entry) {
            if ($entry->type === BankEntry::TYPE_DEPOSIT) {
                $balance += $entry->amount;
            } else {
                $balance -= $entry->amount;
            }
        }

        return $balance;
    }

    // --- CRUD for manual bank entries ---

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:2000',
            'iva_amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:191',
            'project_id' => 'nullable|string',
        ]);

        $company = auth()->user()->company();

        $entry = new BankEntry();
        $entry->company_id = $company->id;
        $entry->user_id = auth()->user()->id;
        $entry->date = $request->date;
        $entry->type = $request->type;
        $entry->amount = $request->amount;
        $entry->iva_amount = $request->get('iva_amount', 0);
        $entry->description = $request->description;
        $entry->category = $request->category;
        $entry->reference = $request->reference;

        if ($request->project_id) {
            $entry->project_id = $this->decodePrimaryKey($request->project_id);
        }

        $entry->save();

        return response()->json(['data' => (new BankEntryTransformer())->transform($entry)], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $decodedId = $this->decodePrimaryKey($id);
        $company = auth()->user()->company();

        $entry = BankEntry::where('company_id', $company->id)
            ->where('id', $decodedId)
            ->firstOrFail();

        $request->validate([
            'date' => 'sometimes|date',
            'type' => 'sometimes|in:deposit,withdrawal',
            'amount' => 'sometimes|numeric|min:0.01',
            'description' => 'nullable|string|max:2000',
            'iva_amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:191',
            'project_id' => 'nullable|string',
        ]);

        $entry->fill($request->only([
            'date', 'type', 'amount', 'iva_amount',
            'description', 'category', 'reference',
        ]));

        if ($request->has('project_id')) {
            $entry->project_id = $request->project_id
                ? $this->decodePrimaryKey($request->project_id)
                : null;
        }

        $entry->save();

        return response()->json(['data' => (new BankEntryTransformer())->transform($entry)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $decodedId = $this->decodePrimaryKey($id);
        $company = auth()->user()->company();

        $entry = BankEntry::where('company_id', $company->id)
            ->where('id', $decodedId)
            ->firstOrFail();

        $entry->is_deleted = true;
        $entry->save();
        $entry->delete();

        return response()->json([], 204);
    }
}
