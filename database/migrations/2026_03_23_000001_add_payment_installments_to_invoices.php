<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('installment_count')->nullable()->after('partial_due_date');
            $table->string('installment_period')->nullable()->after('installment_count');
            $table->json('installment_schedule')->nullable()->after('installment_period');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['installment_count', 'installment_period', 'installment_schedule']);
        });
    }
};
