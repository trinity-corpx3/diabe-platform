<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('descripcion');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('descuento_semanal', 10, 2);
            $table->decimal('saldo_restante', 10, 2);
            $table->integer('semanas_aplicadas')->default(0);
            $table->integer('semanas_estimadas');
            $table->date('fecha_inicio');
            $table->date('fecha_liquidacion_estimada');
            $table->date('fecha_liquidacion_real')->nullable();
            $table->text('notas')->nullable();
            $table->enum('estado', ['activo', 'pausado', 'liquidado', 'cancelado'])->default('activo');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['employee_id', 'estado']);
            $table->index('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_discounts');
    }
};
