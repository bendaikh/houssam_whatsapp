<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedBigInteger('alfa_cod_seller_id')->nullable()->after('tiktok_pixel_enabled');
            $table->string('alfa_cod_seller_name')->nullable()->after('alfa_cod_seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['alfa_cod_seller_id', 'alfa_cod_seller_name']);
        });
    }
};
