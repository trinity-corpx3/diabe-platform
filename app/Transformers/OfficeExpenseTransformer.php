<?php

namespace App\Transformers;

use App\Models\OfficeExpense;
use App\Models\Vendor;
use App\Models\ExpenseCategory;
use App\Utils\Traits\MakesHash;
use League\Fractal\Resource\Item;

class OfficeExpenseTransformer extends EntityTransformer
{
    use MakesHash;

    protected array $availableIncludes = [
        'vendor',
        'category',
    ];

    public function includeCategory(OfficeExpense $officeExpense): ?Item
    {
        $transformer = new ExpenseCategoryTransformer($this->serializer);

        if (!$officeExpense->category) {
            return null;
        }

        return $this->includeItem($officeExpense->category, $transformer, ExpenseCategory::class);
    }

    public function includeVendor(OfficeExpense $officeExpense): ?Item
    {
        $transformer = new VendorTransformer($this->serializer);

        if (!$officeExpense->vendor) {
            return null;
        }

        return $this->includeItem($officeExpense->vendor, $transformer, Vendor::class);
    }

    /**
     * @param OfficeExpense $officeExpense
     *
     * @return array
     */
    public function transform(OfficeExpense $officeExpense)
    {
        return [
            'id' => $this->encodePrimaryKey($officeExpense->id),
            'user_id' => $this->encodePrimaryKey($officeExpense->user_id),
            'vendor_id' => $this->encodePrimaryKey($officeExpense->vendor_id),
            'category_id' => $this->encodePrimaryKey($officeExpense->category_id),
            'amount' => (float) $officeExpense->amount ?: 0,
            'date' => $officeExpense->date ?: '',
            'public_notes' => (string) $officeExpense->public_notes ?: '',
            'private_notes' => (string) $officeExpense->private_notes ?: '',
            'is_deleted' => (bool) $officeExpense->is_deleted,
            'updated_at' => $officeExpense->updated_at ? $officeExpense->updated_at->getTimestamp() : 0,
            'created_at' => $officeExpense->created_at ? $officeExpense->created_at->getTimestamp() : 0,
            'entity_type' => 'office_expense',
        ];
    }
}
