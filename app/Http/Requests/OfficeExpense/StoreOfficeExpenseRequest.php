<?php

namespace App\Http\Requests\OfficeExpense;

use App\Http\Requests\Request;
use App\Models\OfficeExpense;
use App\Utils\Traits\MakesHash;
use Illuminate\Validation\Rule;

class StoreOfficeExpenseRequest extends Request
{
    use MakesHash;
    public function authorize(): bool
    {
        return true; // For now, we can refine this later if needed
    }

    public function rules()
    {
        $user = auth()->user();

        $rules = [
            'vendor_id' => 'bail|nullable|exists:vendors,id,company_id,' . $user->company()->id,
            'category_id' => 'bail|nullable|exists:expense_categories,id,company_id,' . $user->company()->id,
            'date' => 'bail|required|date:Y-m-d',
            'amount' => 'bail|required|numeric|min:0',
            'public_notes' => 'nullable|string',
            'private_notes' => 'nullable|string',
        ];

        return $this->globalRules($rules);
    }

    public function prepareForValidation()
    {
        $input = $this->all();
        $input = $this->decodePrimaryKeys($input);
        $this->replace($input);
    }
}
