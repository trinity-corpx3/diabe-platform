<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('office_expenses')) {
            Schema::create('office_expenses', function (Blueprint ) {
                ->id();
                ->unsignedBigInteger('company_id');
                ->unsignedBigInteger('user_id');
                ->unsignedBigInteger('vendor_id')->nullable();
                ->unsignedBigInteger('category_id')->nullable();
                ->decimal('amount', 12, 2);
                ->date('date');
                ->text('public_notes')->nullable();
                ->text('private_notes')->nullable();
                ->boolean('is_deleted')->default(false);
                ->timestamps();
                ->softDeletes();

                ->index(['company_id', 'date']);
                ->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                ->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
                ->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('expenses', 'office_expense_id')) {
            Schema::table('expenses', function (Blueprint ) {
                ->unsignedBigInteger('office_expense_id')->nullable()->after('project_id');
                ->foreign('office_expense_id')->references('id')->on('office_expenses')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint ) {
            ->dropForeign(['office_expense_id']);
            ->dropColumn('office_expense_id');
        });
        Schema::dropIfExists('office_expenses');
    }
};
