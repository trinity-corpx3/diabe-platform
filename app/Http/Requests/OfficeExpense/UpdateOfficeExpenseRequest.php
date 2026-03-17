<?php

namespace App\Http\Requests\OfficeExpense;

use App\Http\Requests\Request;
use App\Models\OfficeExpense;
use App\Utils\Traits\MakesHash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateOfficeExpenseRequest extends Request
{
    use MakesHash;
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

    public function prepareForValidation()
    {
        $input = $this->all();
        Log::info('OfficeExpense Update Request Input before decoding:', $input);
        $input = $this->decodePrimaryKeys($input);
        Log::info('OfficeExpense Update Request Input after decoding:', $input);
        $this->replace($input);
    }
}
