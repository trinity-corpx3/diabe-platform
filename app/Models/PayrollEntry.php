<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * PayrollEntry Model
 *
 * Represents a daily payroll entry for a worker assigned to a project.
 *
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property int|null $project_id
 * @property string $worker_name
 * @property string $date
 * @property float $daily_wage
 * @property bool $attended
 * @property string|null $notes
 * @property int|null $week_number
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
        'attended',
        'notes',
        'week_number',
    ];

    protected $casts = [
        'daily_wage' => 'float',
        'attended' => 'boolean',
        'date' => 'date',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

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
