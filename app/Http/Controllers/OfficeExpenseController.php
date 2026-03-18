<?php

namespace App\Http\Controllers;

use App\Filters\OfficeExpenseFilters;
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

    public function index(OfficeExpenseFilters $filters)
    {
        if (! request()->has('status')) {
            request()->merge(['status' => 'active']);
        }

        $query = OfficeExpense::filter($filters);

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
                    if (! $officeExpense->trashed()) {
                        $officeExpense->delete();
                    }
                    break;
                case 'restore':
                    if ($officeExpense->is_deleted) {
                        $officeExpense->is_deleted = false;
                        $officeExpense->saveQuietly();
                    }

                    if ($officeExpense->trashed()) {
                        $officeExpense->restore();
                    }
                    break;
                case 'delete':
                    if (! $officeExpense->is_deleted) {
                        $officeExpense->is_deleted = true;
                        $officeExpense->save();
                    }

                    if (! $officeExpense->trashed()) {
                        $officeExpense->delete();
                    }
                    break;
            }
        }

        $refreshed = OfficeExpense::withTrashed()
            ->where('company_id', $companyId)
            ->whereIn('id', $ids);

        return $this->listResponse($refreshed);
    }
}
