<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('office_expenses')) {
            Schema::create('office_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->decimal('amount', 12, 2);
                $table->date('date');
                $table->text('public_notes')->nullable();
                $table->text('private_notes')->nullable();
                $table->boolean('is_deleted')->default(false);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['company_id', 'date']);
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
                $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('expenses', 'office_expense_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('office_expense_id')->nullable()->after('project_id');
                $table->foreign('office_expense_id')->references('id')->on('office_expenses')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['office_expense_id']);
            $table->dropColumn('office_expense_id');
        });
        Schema::dropIfExists('office_expenses');
    }
};
