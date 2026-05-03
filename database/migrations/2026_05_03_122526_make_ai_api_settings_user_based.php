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
        // First, migrate workspace-based settings to user-based settings
        // Get all unique user_ids from ai_api_settings
        $settings = DB::table('ai_api_settings')
            ->whereNotNull('user_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');
        
        // For each user, keep only the most recent setting (delete duplicates)
        foreach ($settings as $userId => $userSettings) {
            if ($userSettings->count() > 1) {
                // Keep the first (most recent) and delete the rest
                $keepId = $userSettings->first()->id;
                DB::table('ai_api_settings')
                    ->where('user_id', $userId)
                    ->where('id', '!=', $keepId)
                    ->delete();
            }
        }
        
        // Now modify the table structure
        Schema::table('ai_api_settings', function (Blueprint $table) {
            // Make workspace_id nullable
            $table->unsignedBigInteger('workspace_id')->nullable()->change();
            
            // Add unique constraint on user_id if it doesn't exist
            if (!$this->hasUniqueIndex('ai_api_settings', 'user_id')) {
                $table->unique('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_api_settings', function (Blueprint $table) {
            // Remove unique constraint on user_id
            if ($this->hasUniqueIndex('ai_api_settings', 'user_id')) {
                $table->dropUnique(['user_id']);
            }
        });
    }
    
    /**
     * Check if a unique index exists on a column
     */
    protected function hasUniqueIndex(string $table, string $column): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name = ? AND Non_unique = 0", [$column]);
        return count($indexes) > 0;
    }
};
