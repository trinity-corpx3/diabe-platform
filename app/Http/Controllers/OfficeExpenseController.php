<?php

namespace App\Http\Controllers;

use App\Models\OfficeExpense;
use App\Transformers\OfficeExpenseTransformer;
use App\Http\Requests\Expense\StoreExpenseRequest; // We can reuse or create a custom one
use App\Http\Requests\Expense\UpdateExpenseRequest;
use Illuminate\Http\Response;

class OfficeExpenseController extends BaseController
{
    protected $entity_type = OfficeExpense::class;
    protected $entity_transformer = OfficeExpenseTransformer::class;

    public function index()
    {
        $officeExpenses = OfficeExpense::where('company_id', request()->user()->company()->id)
            ->where('is_deleted', false)
            ->orderBy('date', 'desc')
            ->get();

        return $this->itemResponse($officeExpenses);
    }

    public function store(StoreExpenseRequest $request)
    {
        $officeExpense = new OfficeExpense();
        $officeExpense->fill($request->all());
        $officeExpense->company_id = $request->user()->company()->id;
        $officeExpense->user_id = $request->user()->id;
        $officeExpense->save();

        return $this->itemResponse($officeExpense);
    }

    public function show(OfficeExpense $officeExpense)
    {
        return $this->itemResponse($officeExpense);
    }

    public function update(UpdateExpenseRequest $request, OfficeExpense $officeExpense)
    {
        $officeExpense->fill($request->all());
        $officeExpense->save();

        return $this->itemResponse($officeExpense->fresh());
    }

    public function destroy(OfficeExpense $officeExpense)
    {
        $officeExpense->delete();
        return $this->itemResponse($officeExpense);
    }
}
