<?php

namespace App\Http\Controllers;

use App\Models\OfficeExpense;
use App\Transformers\OfficeExpenseTransformer;
use App\Http\Requests\OfficeExpense\StoreOfficeExpenseRequest;
use App\Http\Requests\OfficeExpense\UpdateOfficeExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OfficeExpenseController extends BaseController
{
    protected $entity_type = OfficeExpense::class;
    protected $entity_transformer = OfficeExpenseTransformer::class;

    public function index()
    {
        $query = OfficeExpense::where('company_id', request()->user()->company()->id)
            ->where('is_deleted', false)
            ->orderBy('date', 'desc');

        return $this->listResponse($query);
    }

    public function store(StoreOfficeExpenseRequest $request)
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

    public function update(UpdateOfficeExpenseRequest $request, OfficeExpense $officeExpense)
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

    public function bulk(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        $action = (string) $request->input('action', '');

        if (empty($ids) || $action === '') {
            return response()->json(['message' => 'Invalid bulk request'], Response::HTTP_BAD_REQUEST);
        }

        $companyId = $request->user()->company()->id;

        $query = OfficeExpense::withTrashed()
            ->where('company_id', $companyId)
            ->whereIn('id', $ids);

        $officeExpenses = $query->get();

        foreach ($officeExpenses as $officeExpense) {
            switch ($action) {
                case 'archive':
                    $officeExpense->is_deleted = true;
                    $officeExpense->save();
                    $officeExpense->delete();
                    break;
                case 'restore':
                    $officeExpense->restore();
                    $officeExpense->is_deleted = false;
                    $officeExpense->save();
                    break;
                case 'delete':
                    $officeExpense->forceDelete();
                    break;
            }
        }

        $refreshed = OfficeExpense::withTrashed()
            ->where('company_id', $companyId)
            ->whereIn('id', $ids);

        return $this->listResponse($refreshed);
    }
}
