<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Design;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Soft delete all designs except ID 5 (Business)
        // Explicitly bypassing potential scopes by using base query if needed, 
        // but Model should handle SoftDeletes automatically on delete()
        $count = Design::where('id', '!=', 5)->delete();
        echo "Soft deleted $count designs.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore all designs
        Design::withTrashed()->where('id', '!=', 5)->restore();
    }
};
