<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_discounts', function (Blueprint $table) {
            // Agregar payroll_entry_id como foreign key
            $table->unsignedBigInteger('payroll_entry_id')->after('id')->nullable();
            $table->foreign('payroll_entry_id')->references('id')->on('payroll_entries')->onDelete('cascade');
            
            // Renombrar monto_total a monto (más simple)
            $table->renameColumn('monto_total', 'monto');
            
            // Eliminar columnas innecesarias para el modelo simplificado
            $table->dropColumn([
                'descuento_semanal',
                'saldo_restante',
                'semanas_aplicadas',
                'semanas_estimadas',
                'fecha_liquidacion_estimada',
                'fecha_liquidacion_real',
                'estado',
                'fecha_inicio'
            ]);
        });
        
        // Eliminar la tabla de aplicaciones ya que ahora el descuento está vinculado directamente
        Schema::dropIfExists('payroll_discount_applications');
    }

    public function down(): void
    {
        // Recrear tabla de aplicaciones
        Schema::create('payroll_discount_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('payroll_week_id');
            $table->decimal('monto_aplicado', 10, 2);
            $table->timestamps();
            
            $table->foreign('discount_id')->references('id')->on('employee_discounts')->onDelete('cascade');
            $table->foreign('payroll_week_id')->references('id')->on('payroll_entries')->onDelete('cascade');
        });
        
        Schema::table('employee_discounts', function (Blueprint $table) {
            // Revertir cambios
            $table->dropForeign(['payroll_entry_id']);
            $table->dropColumn('payroll_entry_id');
            
            $table->renameColumn('monto', 'monto_total');
            
            $table->decimal('descuento_semanal', 10, 2)->default(0);
            $table->decimal('saldo_restante', 10, 2)->default(0);
            $table->integer('semanas_aplicadas')->default(0);
            $table->integer('semanas_estimadas')->default(1);
            $table->date('fecha_liquidacion_estimada')->nullable();
            $table->date('fecha_liquidacion_real')->nullable();
            $table->string('estado')->default('activo');
            $table->date('fecha_inicio')->nullable();
        });
    }
};
