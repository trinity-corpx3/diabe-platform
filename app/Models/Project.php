<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2025. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Models;

use App\Utils\Number;
use Illuminate\Support\Facades\App;
use Elastic\ScoutDriverPlus\Searchable;
use App\Services\Project\ProjectService;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Project.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $assigned_user_id
 * @property int $company_id
 * @property int|null $client_id
 * @property string $name
 * @property float $task_rate
 * @property string|null $due_date
 * @property string|null $private_notes
 * @property float $budgeted_hours
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $custom_value3
 * @property string|null $custom_value4
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property string|null $public_notes
 * @property bool $is_deleted
 * @property string|null $number
 * @property string $color
 * @property int|null $current_hours
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\Company $company
 * @property-read int|null $documents_count
 * @property-read mixed $hashed_id
 * @property-read Project|null $project
 * @property-read int|null $tasks_count
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User $assigned_user
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel company()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel exclude($columns)
 * @method static \Database\Factories\ProjectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Project filter(\App\Filters\QueryFilters $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel scope()
 * @method static \Illuminate\Database\Eloquent\Builder|Project withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Project withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quote> $quotes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @mixin \Eloquent
 */
class Project extends BaseModel
{
    use SoftDeletes;
    use PresentableTrait;
    use Filterable;
    use Searchable;

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs(): string
    {
        return 'projects';
    }

    protected $fillable = [
        'name',
        'client_id',
        'task_rate',
        'private_notes',
        'public_notes',
        'due_date',
        'budgeted_hours',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'assigned_user_id',
        'color',
        'number',
    ];

    protected $with = [
        'documents',
    ];

    protected $touches = [];

    public function getEntityType()
    {
        return self::class;
    }

    public function toSearchableArray()
    {
        $locale = $this->company->locale();
        App::setLocale($locale);

        return [
            'id' => (string)$this->company->db.":".$this->id,
            'name' => ctrans('texts.project') . " " . $this->number . ' | ' . $this->name .  " | " . $this->client->present()->name(),
            'hashed_id' => $this->hashed_id,
            'number' => (string)$this->number,
            'is_deleted' => $this->is_deleted,
            'task_rate' => (float) $this->task_rate,
            'budgeted_hours' => (float) $this->budgeted_hours,
            'due_date' => $this->due_date,
            'custom_value1' => (string)$this->custom_value1,
            'custom_value2' => (string)$this->custom_value2,
            'custom_value3' => (string)$this->custom_value3,
            'custom_value4' => (string)$this->custom_value4,
            'company_key' => $this->company->company_key,
            'private_notes' => (string) $this->private_notes ?: '',
            'public_notes' => (string) $this->public_notes ?: '',
            'current_hours' => (int) $this->current_hours ?: 0,
        ];
    }
    
    public function getScoutKey()
    {
        return (string)$this->company->db.":".$this->id;
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class)->withTrashed();
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function assigned_user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'id')->withTrashed();
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class)->withTrashed();
    }

    public function quotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Total invoiced from valid (Sent/Partial/Paid, not deleted) invoices.
     */
    public function calcTotalInvoiced(): float
    {
        return (float) $this->invoices()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereIn('status_id', [
                Invoice::STATUS_SENT,
                Invoice::STATUS_PARTIAL,
                Invoice::STATUS_PAID,
            ])
            ->sum('amount');
    }

    /**
     * Total collected from client, derived from invoice (amount - balance).
     */
    public function calcTotalPaid(): float
    {
        $invoiced = $this->calcTotalInvoiced();

        $balance = (float) $this->invoices()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereIn('status_id', [
                Invoice::STATUS_SENT,
                Invoice::STATUS_PARTIAL,
                Invoice::STATUS_PAID,
            ])
            ->sum('balance');

        return $invoiced - $balance;
    }

    /**
     * Total expenses already paid (with payment_date).
     */
    public function calcTotalExpenses(): float
    {
        return (float) $this->expenses()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereNotNull('payment_date')
            ->sum('amount');
    }

    /**
     * Total expenses registered but not yet paid (no payment_date).
     */
    public function calcTotalExpensesPending(): float
    {
        return (float) $this->expenses()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereNull('payment_date')
            ->sum('amount');
    }

    /**
     * Full financial summary for this project.
     */
    public function financialSummary(): array
    {
        $total_invoiced = $this->calcTotalInvoiced();
        $total_paid = $this->calcTotalPaid();
        $total_expenses = $this->calcTotalExpenses();
        $total_expenses_pending = $this->calcTotalExpensesPending();

        $profit = round($total_paid - $total_expenses, 2);
        $profitability = $total_invoiced > 0
            ? round(($profit / $total_invoiced) * 100, 1)
            : 0.0;

        // IVA from client payments applied to this project's invoices
        $iva_cobrado = $this->calcIvaCobrado();
        // IVA from paid expenses
        $iva_acreditado = $this->calcIvaAcreditado();

        return [
            'total_invoiced' => round($total_invoiced, 2),
            'total_paid_by_client' => round($total_paid, 2),
            'pending_collection' => round($total_invoiced - $total_paid, 2),
            'total_expenses' => round($total_expenses, 2),
            'total_expenses_pending' => round($total_expenses_pending, 2),
            'profit' => $profit,
            'profitability' => $profitability,
            'iva_cobrado' => round($iva_cobrado, 2),
            'iva_acreditado' => round($iva_acreditado, 2),
            'iva_por_pagar' => round($iva_cobrado - $iva_acreditado, 2),
        ];
    }

    /**
     * IVA collected: derived from payments applied to this project's invoices.
     */
    public function calcIvaCobrado(): float
    {
        $payments = $this->payments()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->where('status_id', '!=', Payment::STATUS_VOIDED)
            ->with('invoices')
            ->get();

        $total = 0.0;

        foreach ($payments as $payment) {
            foreach ($payment->invoices as $invoice) {
                if ($invoice->pivot->deleted_at) {
                    continue;
                }
                $applied = (float) $invoice->pivot->amount;
                $rate = (float) ($invoice->tax_rate1 ?? 0);
                if ($rate > 0 && $applied > 0) {
                    $total += $applied * $rate / (100 + $rate);
                }
            }
        }

        return $total;
    }

    /**
     * IVA credited: sum of tax amounts from paid expenses.
     */
    public function calcIvaAcreditado(): float
    {
        $expenses = $this->expenses()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereNotNull('payment_date')
            ->get();

        $total = 0.0;
        foreach ($expenses as $expense) {
            $total += $expense->getTaxAmount();
        }

        return $total;
    }

    /**
    * Service entry points.
    *
    * @return ProjectService
    */
    public function service(): ProjectService
    {
        return new ProjectService($this);
    }

    public function translate_entity()
    {
        return ctrans('texts.project');
    }
}
