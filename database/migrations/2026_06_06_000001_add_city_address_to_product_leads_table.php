<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            $table->string('city')->nullable()->after('phone');
            $table->string('address')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            $table->dropColumn(['city', 'address']);
        });
    }
};
