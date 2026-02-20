<?php

namespace App\Models;

use App\Utils\Traits\MakesHash;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankEntry extends BaseModel
{
    use SoftDeletes;
    use MakesHash;
    use Filterable;

    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAWAL = 'withdrawal';

    protected $fillable = [
        'project_id',
        'date',
        'type',
        'description',
        'amount',
        'iva_amount',
        'category',
        'reference',
    ];

    protected $casts = [
        'amount' => 'float',
        'iva_amount' => 'float',
        'date' => 'date:Y-m-d',
        'is_deleted' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }
}
