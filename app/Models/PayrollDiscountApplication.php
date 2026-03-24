<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDiscountApplication extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'discount_id',
        'payroll_week_id',
        'monto_aplicado',
    ];

    protected $casts = [
        'monto_aplicado' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            $application->created_at = now();
        });
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(EmployeeDiscount::class, 'discount_id');
    }

    public function payrollWeek(): BelongsTo
    {
        return $this->belongsTo(PayrollEntry::class, 'payroll_week_id');
    }
}
