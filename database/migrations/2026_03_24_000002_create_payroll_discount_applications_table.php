<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_discount_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('payroll_week_id');
            $table->decimal('monto_aplicado', 10, 2);
            $table->timestamp('created_at');

            $table->foreign('discount_id')->references('id')->on('employee_discounts')->onDelete('cascade');
            $table->foreign('payroll_week_id')->references('id')->on('payroll_entries')->onDelete('cascade');
            
            $table->index(['discount_id', 'payroll_week_id']);
            $table->index('payroll_week_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_discount_applications');
    }
};
