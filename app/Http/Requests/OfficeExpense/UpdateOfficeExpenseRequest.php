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
            'vendor_id' => 'bail|sometimes|nullable|exists:vendors,id,company_id,' . $user->company()->id,
            'category_id' => 'bail|sometimes|nullable|exists:expense_categories,id,company_id,' . $user->company()->id,
            'date' => 'bail|sometimes|required|date:Y-m-d',
            'amount' => 'bail|sometimes|required|numeric|min:0',
            'public_notes' => 'nullable|string',
            'private_notes' => 'nullable|string',
        ];

        return $this->globalRules($rules);
    }
}
