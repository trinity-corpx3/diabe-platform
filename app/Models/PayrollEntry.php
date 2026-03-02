<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * PayrollEntry Model — Weekly payroll with overtime.
 *
 * Each record = one worker's pay for one week on one project.
 *
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property int|null $project_id
 * @property string $worker_name
 * @property string $date
 * @property float $daily_wage         (legacy, unused in weekly mode)
 * @property float $base_weekly_wage   Fixed weekly salary
 * @property float $overtime_hours     Number of overtime hours
 * @property float $overtime_rate      Rate per overtime hour (MXN)
 * @property int $days_worked          Days worked that week (default 6)
 * @property bool $attended            (legacy, unused in weekly mode)
 * @property string|null $notes
 * @property int|null $week_number     ISO week number
 */
class PayrollEntry extends BaseModel
{
    use SoftDeletes;
    use Filterable;

    protected $fillable = [
        'company_id',
        'user_id',
        'project_id',
        'worker_name',
        'date',
        'daily_wage',
        'base_weekly_wage',
        'overtime_hours',
        'overtime_rate',
        'days_worked',
        'attended',
        'notes',
        'week_number',
    ];

    protected $casts = [
        'daily_wage' => 'float',
        'base_weekly_wage' => 'float',
        'overtime_hours' => 'float',
        'overtime_rate' => 'float',
        'days_worked' => 'integer',
        'attended' => 'boolean',
        'date' => 'date',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * Computed: overtime pay = overtime_hours × overtime_rate.
     */
    public function getOvertimePayAttribute(): float
    {
        return round($this->overtime_hours * $this->overtime_rate, 2);
    }

    /**
     * Computed: total pay = base_weekly_wage + overtime pay.
     */
    public function getTotalPayAttribute(): float
    {
        return round($this->base_weekly_wage + $this->overtime_pay, 2);
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getEntityType()
    {
        return self::class;
    }

    public function translate_entity(): string
    {
        return 'Nómina';
    }
}
