<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_pixels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('pixel_id');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['store_id', 'pixel_id']);
        });

        // Migrate existing single-pixel configuration
        if (Schema::hasColumn('stores', 'facebook_pixel_id')) {
            $stores = DB::table('stores')
                ->whereNotNull('facebook_pixel_id')
                ->where('facebook_pixel_id', '!=', '')
                ->get(['id', 'facebook_pixel_id', 'facebook_pixel_enabled']);

            foreach ($stores as $store) {
                DB::table('facebook_pixels')->insert([
                    'store_id' => $store->id,
                    'name' => 'Primary Pixel',
                    'pixel_id' => $store->facebook_pixel_id,
                    'is_enabled' => (bool) $store->facebook_pixel_enabled,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_pixels');
    }
};
