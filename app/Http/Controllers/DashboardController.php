<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Utils\PaymentTerms;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function pendingPayments(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();
        $today = Carbon::now();

        $invoices = Invoice::query()
            ->with(['client', 'payments'])
            ->where('company_id', auth()->user()->company()->id)
            ->where('is_deleted', 0)
            ->where('balance', '>', 0)
            ->get();

        $pendingPayments = [];

        foreach ($invoices as $invoice) {
            // Get payment terms from client or invoice
            $paymentTerms = $invoice->client->getSetting('payment_terms') ?? '';
            
            // Skip if not monthly terms (must be > 100)
            if (!PaymentTerms::isMonthlyTerm($paymentTerms)) {
                continue;
            }

            $months = PaymentTerms::getMonths($paymentTerms);
            
            // Skip if no months or only 1 month (not installments)
            if ($months <= 1) {
                continue;
            }

            // Generate installment schedule dynamically
            $schedule = $this->generateInstallmentSchedule($invoice, $months);
            $installmentAmount = $invoice->amount / $months;

            foreach ($schedule as $index => $installment) {
                $dueDate = Carbon::parse($installment['due_date']);
                
                // Include overdue installments and current month installments
                if ($dueDate->lte($endOfMonth)) {
                    $installmentNumber = $index + 1;
                    
                    // Calculate paid amount for this installment
                    $paidAmount = $this->calculatePaidAmountForInstallment(
                        $invoice,
                        $installmentNumber,
                        $installmentAmount
                    );

                    $isPaid = $paidAmount >= $installmentAmount;
                    $isPartial = $paidAmount > 0 && $paidAmount < $installmentAmount;
                    $isOverdue = $dueDate->lt($today) && !$isPaid;
                    $isPending = $dueDate->gte($today) && !$isPaid;

                    // Only include if in current month or overdue
                    if ($dueDate->between($startOfMonth, $endOfMonth) || $isOverdue) {
                        $pendingPayments[] = [
                            'invoice_id' => $invoice->id,
                            'invoice_number' => $invoice->number,
                            'client_id' => $invoice->client_id,
                            'client_name' => $invoice->client->name ?? $invoice->client->display_name,
                            'installment_number' => $installmentNumber,
                            'installment_total' => $months,
                            'due_date' => $dueDate->format('Y-m-d'),
                            'amount' => $installmentAmount,
                            'paid_amount' => $paidAmount,
                            'pending_amount' => max(0, $installmentAmount - $paidAmount),
                            'status' => $isPaid ? 'paid' : ($isPartial ? 'partial' : ($isOverdue ? 'overdue' : 'pending')),
                            'currency_id' => $invoice->client->settings->currency_id ?? $invoice->company->settings->currency_id,
                        ];
                    }
                }
            }
        }

        // Sort: overdue first (desc), then pending (asc), then paid
        usort($pendingPayments, function ($a, $b) {
            $statusOrder = ['overdue' => 1, 'partial' => 2, 'pending' => 3, 'paid' => 4];
            
            if ($a['status'] !== $b['status']) {
                return $statusOrder[$a['status']] <=> $statusOrder[$b['status']];
            }
            
            if ($a['status'] === 'overdue') {
                return $b['due_date'] <=> $a['due_date'];
            }
            
            return $a['due_date'] <=> $b['due_date'];
        });

        // Calculate summary
        $summary = [
            'total_pending' => 0,
            'count_overdue' => 0,
            'count_pending' => 0,
            'count_paid' => 0,
            'count_partial' => 0,
        ];

        foreach ($pendingPayments as $payment) {
            if ($payment['status'] === 'overdue') {
                $summary['count_overdue']++;
                $summary['total_pending'] += $payment['pending_amount'];
            } elseif ($payment['status'] === 'pending') {
                $summary['count_pending']++;
                $summary['total_pending'] += $payment['pending_amount'];
            } elseif ($payment['status'] === 'partial') {
                $summary['count_partial']++;
                $summary['total_pending'] += $payment['pending_amount'];
            } elseif ($payment['status'] === 'paid') {
                $summary['count_paid']++;
            }
        }

        return response()->json([
            'data' => $pendingPayments,
            'summary' => $summary,
            'month' => $month,
            'last_updated' => Carbon::now()->toIso8601String(),
        ]);
    }

    private function calculatePaidAmountForInstallment(Invoice $invoice, int $installmentNumber, float $installmentAmount): float
    {
        $payments = $invoice->payments()
            ->whereIn('status_id', [Payment::STATUS_COMPLETED, Payment::STATUS_PARTIALLY_REFUNDED])
            ->orderBy('date', 'asc')
            ->get();

        $totalPaid = 0;
        $remainingToAllocate = 0;

        foreach ($payments as $payment) {
            $remainingToAllocate += $payment->amount;
        }

        // Allocate payments to installments in order
        for ($i = 1; $i <= $installmentNumber; $i++) {
            if ($remainingToAllocate >= $installmentAmount) {
                $remainingToAllocate -= $installmentAmount;
                if ($i === $installmentNumber) {
                    $totalPaid = $installmentAmount;
                }
            } else {
                if ($i === $installmentNumber) {
                    $totalPaid = $remainingToAllocate;
                }
                break;
            }
        }

        return $totalPaid;
    }

    private function generateInstallmentSchedule(Invoice $invoice, int $months): array
    {
        $schedule = [];
        $invoiceDate = Carbon::parse($invoice->date);
        
        for ($i = 0; $i < $months; $i++) {
            $dueDate = $invoiceDate->copy()->addMonthsNoOverflow($i + 1);
            
            $schedule[] = [
                'number' => $i + 1,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => $invoice->amount / $months,
            ];
        }
        
        return $schedule;
    }
}
