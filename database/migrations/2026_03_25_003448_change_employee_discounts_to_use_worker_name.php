<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_discounts', function (Blueprint $table) {
            // Agregar columna worker_name
            $table->string('worker_name')->after('employee_id')->nullable();
            
            // Hacer employee_id nullable
            $table->unsignedBigInteger('employee_id')->nullable()->change();
        });
        
        // Copiar datos: convertir employee_id a worker_name desde payroll_entries
        DB::statement("
            UPDATE employee_discounts ed
            LEFT JOIN users u ON ed.employee_id = u.id
            SET ed.worker_name = CONCAT(u.first_name, ' ', u.last_name)
            WHERE ed.employee_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('employee_discounts', function (Blueprint $table) {
            $table->dropColumn('worker_name');
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
        });
    }
};
