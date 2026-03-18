<?php

namespace App\Observers;

use App\Models\OfficeExpense;

class OfficeExpenseObserver
{
    /**
     * Handle the OfficeExpense "created" event.
     *
     * @param  \App\Models\OfficeExpense  $officeExpense
     * @return void
     */
    public function created(OfficeExpense $officeExpense)
    {
    }

    /**
     * Handle the OfficeExpense "updated" event.
     *
     * @param  \App\Models\OfficeExpense  $officeExpense
     * @return void
     */
    public function updated(OfficeExpense $officeExpense)
    {
    }

    /**
     * Handle the OfficeExpense "deleted" event.
     *
     * @param  \App\Models\OfficeExpense  $officeExpense
     * @return void
     */
    public function deleted(OfficeExpense $officeExpense)
    {
        $officeExpense->expenses()->delete();
    }
}
