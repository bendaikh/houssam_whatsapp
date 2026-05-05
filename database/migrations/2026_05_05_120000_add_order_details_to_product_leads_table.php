<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('product_leads', 'selected_promotion_id')) {
                $table->unsignedBigInteger('selected_promotion_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_leads', 'selected_variation_id')) {
                $table->unsignedBigInteger('selected_variation_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_leads', 'selected_price')) {
                $table->decimal('selected_price', 10, 2)->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_leads', 'status')) {
                $table->string('status')->default('pending')->after('user_agent');
            }
        });
        
        // Add foreign keys separately to avoid errors
        try {
            Schema::table('product_leads', function (Blueprint $table) {
                $table->foreign('selected_promotion_id')->references('id')->on('product_promotions')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key may already exist
        }
        
        try {
            Schema::table('product_leads', function (Blueprint $table) {
                $table->foreign('selected_variation_id')->references('id')->on('product_variations')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key may already exist
        }
    }

    public function down(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            try {
                $table->dropForeign(['selected_promotion_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['selected_variation_id']);
            } catch (\Exception $e) {}
            
            $columns = ['selected_promotion_id', 'selected_variation_id', 'selected_price', 'status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('product_leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
