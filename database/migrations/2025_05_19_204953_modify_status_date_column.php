<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            // First, create a temporary column
            $table->timestamp('status_date_new')->nullable();
        });

        // Copy existing data with time set to current time
        DB::statement('UPDATE statuses SET status_date_new = status_date::date + CURRENT_TIME');

        Schema::table('statuses', function (Blueprint $table) {
            // Drop the old column
            $table->dropColumn('status_date');
            
            // Rename the new column to the original name
            $table->renameColumn('status_date_new', 'status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            // Convert back to date if needed
            $table->date('status_date_old')->nullable();
        });

        // Copy data back (losing time information)
        DB::statement('UPDATE statuses SET status_date_old = status_date::date');

        Schema::table('statuses', function (Blueprint $table) {
            // Drop the timestamp column
            $table->dropColumn('status_date');
            
            // Rename back to original
            $table->renameColumn('status_date_old', 'status_date');
        });
    }
};
