<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add weekly payroll fields: base_weekly_wage, overtime_hours, overtime_rate, days_worked.
 * Keeps legacy daily_wage/attended columns for backward compatibility.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('payroll_entries', 'base_weekly_wage')) {
            return;
        }

        Schema::table('payroll_entries', function (Blueprint $table) {
            $table->decimal('base_weekly_wage', 12, 2)->default(0)->after('daily_wage');
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('base_weekly_wage');
            $table->decimal('overtime_rate', 12, 2)->default(0)->after('overtime_hours');
            $table->unsignedSmallInteger('days_worked')->default(6)->after('overtime_rate');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_entries', function (Blueprint $table) {
            $table->dropColumn(['base_weekly_wage', 'overtime_hours', 'overtime_rate', 'days_worked']);
        });
    }
};
