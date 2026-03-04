<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Project;
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
        $this->prorate($officeExpense);
    }

    /**
     * Handle the OfficeExpense "updated" event.
     *
     * @param  \App\Models\OfficeExpense  $officeExpense
     * @return void
     */
    public function updated(OfficeExpense $officeExpense)
    {
        // If amount changed or it was just restored, we redistribution
        if ($officeExpense->isDirty('amount') || $officeExpense->wasRecentlyCreated) {
            $this->prorate($officeExpense);
        }
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

    /**
     * Prorate the office expense among active projects.
     *
     * @param  \App\Models\OfficeExpense  $officeExpense
     * @return void
     */
    private function prorate(OfficeExpense $officeExpense)
    {
        // Deleting existing child expenses for clean redistribution on update
        $officeExpense->expenses()->delete();

        $activeProjects = Project::query()->where('company_id', $officeExpense->company_id)
            ->where('is_deleted', '=', 0)
            ->whereNull('deleted_at')
            ->get();

        $projectCount = $activeProjects->count();

        if ($projectCount === 0) {
            return;
        }

        $totalAmount = (float) $officeExpense->amount;
        $baseAmount = floor(($totalAmount / $projectCount) * 100) / 100;
        $remainder = round($totalAmount - ($baseAmount * $projectCount), 2);

        foreach ($activeProjects as $index => $project) {
            $amount = $baseAmount;

            // Add the "orphan centavo" to the last project
            if ($index === $projectCount - 1) {
                $amount = round($baseAmount + $remainder, 2);
            }

            Expense::create([
                'company_id' => $officeExpense->company_id,
                'user_id' => $officeExpense->user_id,
                'vendor_id' => $officeExpense->vendor_id,
                'category_id' => $officeExpense->category_id,
                'project_id' => $project->id,
                'office_expense_id' => $officeExpense->id,
                'amount' => $amount,
                'date' => $officeExpense->date,
                'public_notes' => "Gasto de Oficina prorrateado (#{$officeExpense->id})",
                'private_notes' => "Generado automáticamente por Gasto de Oficina #{$officeExpense->id}",
            ]);
        }
    }
}
