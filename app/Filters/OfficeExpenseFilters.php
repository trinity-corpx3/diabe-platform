<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2025. Invoice Ninja LLC (https://invoiceninja.com)
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class OfficeExpenseFilters extends QueryFilters
{
    public function filter(string $filter = ''): Builder
    {
        if (strlen($filter) == 0) {
            return $this->builder;
        }

        return $this->builder->where(function ($query) use ($filter) {
            $query->where('amount', 'like', '%'.$filter.'%')
                ->orWhere('public_notes', 'like', '%'.$filter.'%')
                ->orWhere('private_notes', 'like', '%'.$filter.'%')
                ->orWhereHas('vendor', function ($q) use ($filter) {
                    $q->where('name', 'like', '%'.$filter.'%');
                })
                ->orWhereHas('category', function ($q) use ($filter) {
                    $q->where('name', 'like', '%'.$filter.'%');
                });
        });
    }

    public function sort(string $sort = ''): Builder
    {
        if (strlen($sort) == 0) {
            return $this->builder->orderBy('date', 'desc');
        }

        $sort_col = explode('|', $sort);

        if (!is_array($sort_col) || count($sort_col) != 2 || !in_array($sort_col[0], \Illuminate\Support\Facades\Schema::getColumnListing($this->builder->getModel()->getTable()))) {
            return $this->builder->orderBy('date', 'desc');
        }

        $dir = $sort_col[1] == 'asc' ? 'asc' : 'desc';

        if ($sort_col[0] == 'vendor_id') {
            return $this->builder
                ->orderByRaw('ISNULL(vendor_id), vendor_id '.$dir)
                ->orderBy(\App\Models\Vendor::select('name')
                ->whereColumn('vendors.id', 'office_expenses.vendor_id'), $dir);
        }

        if ($sort_col[0] == 'category_id') {
            return $this->builder
                ->orderByRaw('ISNULL(category_id), category_id '.$dir)
                ->orderBy(\App\Models\ExpenseCategory::select('name')
                ->whereColumn('expense_categories.id', 'office_expenses.category_id'), $dir);
        }

        return $this->builder->orderBy($sort_col[0], $dir);
    }

    public function entityFilter(): Builder
    {
        return $this->builder->company();
    }
}
