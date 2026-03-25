<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeDiscount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payroll_entry_id',
        'worker_name',
        'descripcion',
        'monto',
        'notas',
        'created_by',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];


    public function payrollEntry(): BelongsTo
    {
        return $this->belongsTo(PayrollEntry::class, 'payroll_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForPayrollEntry($query, $payrollEntryId)
    {
        return $query->where('payroll_entry_id', $payrollEntryId);
    }
}
