<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     * NOTE: Avoid referencing Eloquent models/constants in migrations.
     * Use raw values instead to prevent failures if the model changes.
     */
    public function up()
    {
        // Ensure the mobile payment type exists (id = 54 = PaymentType::MOBILE_PAYMENT)
        DB::table('payment_types')->updateOrInsert(
            ['id' => 54],
            ['name' => 'Mobile Payment']
        );

        // Add Payware gateway entry (example values)
        Schema::table('company_gateways', function (Blueprint $table) {
            // Add any necessary columns if they don't exist
            if (!Schema::hasColumn('company_gateways', 'payware_enabled')) {
                $table->boolean('payware_enabled')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_gateways', function (Blueprint $table) {
            if (Schema::hasColumn('company_gateways', 'payware_enabled')) {
                $table->dropColumn('payware_enabled');
            }
        });
    }
};
