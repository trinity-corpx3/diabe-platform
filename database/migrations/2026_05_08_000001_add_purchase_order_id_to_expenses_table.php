<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseOrderIdToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('expenses', 'purchase_order_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedInteger('purchase_order_id')->nullable()->index()->after('project_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('expenses', 'purchase_order_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('purchase_order_id');
            });
        }
    }
}
