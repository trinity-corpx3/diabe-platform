<?php

namespace App\Http\Requests\OfficeExpense;

use App\Http\Requests\Request;
use App\Models\OfficeExpense;
use Illuminate\Validation\Rule;

class StoreOfficeExpenseRequest extends Request
{
    public function authorize(): bool
    {
        return true; // For now, we can refine this later if needed
    }

    public function rules()
    {
        $user = auth()->user();

        $rules = [
            'vendor_id' => 'bail|required|exists:vendors,id,company_id,' . $user->company()->id,
            'category_id' => 'bail|required|exists:expense_categories,id,company_id,' . $user->company()->id,
            'date' => 'bail|required|date:Y-m-d',
            'amount' => 'bail|required|numeric|min:0',
            'tax_amount' => 'bail|nullable|numeric|min:0',
            'total_amount' => 'bail|required|numeric|min:0',
            'is_prorated' => 'boolean',
            'notes' => 'nullable|string',
        ];

        return $this->globalRules($rules);
    }
}
