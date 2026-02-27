<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('payroll_entries')) {
            return;
        }

        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('worker_name');
            $table->date('date');
            $table->decimal('daily_wage', 12, 2)->default(0);
            $table->boolean('attended')->default(true);
            $table->string('notes')->nullable();
            $table->unsignedInteger('week_number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'date']);
            $table->index(['company_id', 'worker_name']);
            $table->index(['company_id', 'project_id']);
            $table->index(['company_id', 'week_number']);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_entries');
    }
};
