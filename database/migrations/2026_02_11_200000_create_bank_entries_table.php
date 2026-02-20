<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('project_id')->nullable();
            $table->date('date');
            $table->string('type', 20); // deposit | withdrawal
            $table->text('description')->nullable();
            $table->decimal('amount', 16, 4)->default(0);
            $table->decimal('iva_amount', 16, 4)->default(0);
            $table->string('category', 50)->nullable();
            $table->string('reference', 191)->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->softDeletes('deleted_at', 6);
            $table->timestamps(6);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_entries');
    }
};
