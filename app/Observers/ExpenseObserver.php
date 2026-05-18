<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2025. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Observers;

use App\Jobs\Util\WebhookHandler;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Webhook;

class ExpenseObserver
{
    public $afterCommit = true;

    /**
     * Handle the expense "created" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function created(Expense $expense)
    {
        $subscriptions = Webhook::where('company_id', $expense->company_id)
                            ->where('event_id', Webhook::EVENT_CREATE_EXPENSE)
                            ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch(Webhook::EVENT_CREATE_EXPENSE, $expense, $expense->company)->delay(0);
        }

        $this->updatePurchaseOrderBalance($expense);
    }

    /**
     * Handle the expense "updated" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function updated(Expense $expense)
    {
        $event = Webhook::EVENT_UPDATE_EXPENSE;

        if ($expense->getOriginal('deleted_at') && !$expense->deleted_at) {
            $event = Webhook::EVENT_RESTORE_EXPENSE;
        }

        if ($expense->is_deleted) {
            $event = Webhook::EVENT_DELETE_EXPENSE;
        }


        $subscriptions = Webhook::where('company_id', $expense->company_id)
                                    ->where('event_id', $event)
                                    ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch($event, $expense, $expense->company)->delay(0);
        }

        // If the PO link changed, refresh the OLD PO too so its paid_to_date
        // stops including this expense.
        $originalPoId = $expense->getOriginal('purchase_order_id');
        if ($originalPoId && $originalPoId != $expense->purchase_order_id) {
            $this->recalcPurchaseOrder((int) $originalPoId);
        }

        $this->updatePurchaseOrderBalance($expense);
    }

    /**
     * Handle the expense "deleted" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function deleted(Expense $expense)
    {
        if ($expense->is_deleted) {
            return;
        }

        $subscriptions = Webhook::where('company_id', $expense->company_id)
                            ->where('event_id', Webhook::EVENT_ARCHIVE_EXPENSE)
                            ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch(Webhook::EVENT_ARCHIVE_EXPENSE, $expense, $expense->company)->delay(0);
        }

        $this->updatePurchaseOrderBalance($expense);
    }

    /**
     * Handle the expense "restored" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function restored(Expense $expense)
    {
        //
    }

    /**
     * Handle the expense "force deleted" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function forceDeleted(Expense $expense)
    {
        //
    }

    /**
     * Updates the balance of the associated Purchase Order by summing all paid expenses.
     */
    private function updatePurchaseOrderBalance(Expense $expense): void
    {
        if ($expense->purchase_order_id) {
            $this->recalcPurchaseOrder((int) $expense->purchase_order_id);
        }
    }

    /**
     * Recalculate paid_to_date and balance for a given PO from its actual
     * linked, non-deleted, paid expenses.
     */
    private function recalcPurchaseOrder(int $purchaseOrderId): void
    {
        $purchaseOrder = PurchaseOrder::find($purchaseOrderId);
        if (!$purchaseOrder) {
            return;
        }

        $totalPaid = \DB::table('expenses')
            ->where('purchase_order_id', $purchaseOrder->id)
            ->where('payment_date', '!=', '')
            ->whereNotNull('payment_date')
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->sum('amount');

        $purchaseOrder->paid_to_date = (float) $totalPaid;
        $purchaseOrder->balance = round($purchaseOrder->amount - $totalPaid, 2);
        $purchaseOrder->saveQuietly();
    }
}
