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
        'employee_id',
        'descripcion',
        'monto_total',
        'descuento_semanal',
        'saldo_restante',
        'semanas_aplicadas',
        'semanas_estimadas',
        'fecha_inicio',
        'fecha_liquidacion_estimada',
        'fecha_liquidacion_real',
        'notas',
        'estado',
        'created_by',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'descuento_semanal' => 'decimal:2',
        'saldo_restante' => 'decimal:2',
        'semanas_aplicadas' => 'integer',
        'semanas_estimadas' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_liquidacion_estimada' => 'date',
        'fecha_liquidacion_real' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($discount) {
            // Calcular semanas estimadas
            $discount->semanas_estimadas = (int) ceil($discount->monto_total / $discount->descuento_semanal);
            
            // Calcular fecha de liquidación estimada
            $discount->fecha_liquidacion_estimada = $discount->fecha_inicio
                ->addDays($discount->semanas_estimadas * 7);
            
            // Inicializar saldo restante
            if (!isset($discount->saldo_restante)) {
                $discount->saldo_restante = $discount->monto_total;
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PayrollDiscountApplication::class, 'discount_id');
    }

    public function scopeActive($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function isLiquidado(): bool
    {
        return $this->saldo_restante <= 0 || $this->estado === 'liquidado';
    }

    public function getProgresoPercentage(): float
    {
        if ($this->semanas_estimadas == 0) {
            return 0;
        }
        return ($this->semanas_aplicadas / $this->semanas_estimadas) * 100;
    }

    public function aplicarDescuento(float $montoDisponible, $payrollWeekId): ?float
    {
        if ($this->estado !== 'activo' || $this->saldo_restante <= 0) {
            return null;
        }

        // Calcular monto a aplicar (el menor entre descuento_semanal, saldo_restante y monto disponible)
        $montoAplicado = min($this->descuento_semanal, $this->saldo_restante, $montoDisponible);

        if ($montoAplicado <= 0) {
            return null;
        }

        // Registrar la aplicación
        PayrollDiscountApplication::create([
            'discount_id' => $this->id,
            'payroll_week_id' => $payrollWeekId,
            'monto_aplicado' => $montoAplicado,
        ]);

        // Actualizar el descuento
        $this->saldo_restante -= $montoAplicado;
        $this->semanas_aplicadas++;

        // Si se liquidó completamente
        if ($this->saldo_restante <= 0) {
            $this->estado = 'liquidado';
            $this->fecha_liquidacion_real = now();
        }

        $this->save();

        return $montoAplicado;
    }
}
