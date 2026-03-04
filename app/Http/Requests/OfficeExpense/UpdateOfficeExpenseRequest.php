<?php

namespace App\Http\Requests\OfficeExpense;

use App\Http\Requests\Request;
use App\Models\OfficeExpense;
use Illuminate\Validation\Rule;

class UpdateOfficeExpenseRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $user = auth()->user();

        $rules = [
            'vendor_id' => 'bail|sometimes|required|exists:vendors,id,company_id,' . $user->company()->id,
            'category_id' => 'bail|sometimes|required|exists:expense_categories,id,company_id,' . $user->company()->id,
            'date' => 'bail|sometimes|required|date:Y-m-d',
            'amount' => 'bail|sometimes|required|numeric|min:0',
            'tax_amount' => 'bail|nullable|numeric|min:0',
            'total_amount' => 'bail|sometimes|required|numeric|min:0',
            'is_prorated' => 'boolean',
            'notes' => 'nullable|string',
        ];

        return $this->globalRules($rules);
    }
}
