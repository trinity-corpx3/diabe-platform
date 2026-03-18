<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OfficeExpense.
 *
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property int|null $vendor_id
 * @property int|null $category_id
 * @property float $amount
 * @property string $date
 * @property string|null $public_notes
 * @property string|null $private_notes
 * @property bool $is_deleted
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vendor|null $vendor
 * @property-read \App\Models\ExpenseCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 */
class OfficeExpense extends BaseModel
{
    use SoftDeletes;
    use Filterable;

    protected $fillable = [
        'company_id',
        'user_id',
        'vendor_id',
        'category_id',
        'amount',
        'date',
        'public_notes',
        'private_notes',
        'is_deleted',
    ];

    protected $casts = [
        'amount' => 'float',
        'is_deleted' => 'boolean',
        'date' => 'date:Y-m-d',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
