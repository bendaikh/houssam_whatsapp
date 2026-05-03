<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('form_fields')->nullable()->after('landing_page_sections');
        });

        Schema::table('product_leads', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('form_fields');
        });

        Schema::table('product_leads', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
