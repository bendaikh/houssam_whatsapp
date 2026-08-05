<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('system_connect_url')->nullable()->after('alfa_cod_seller_name');
            $table->text('system_connect_key_encrypted')->nullable()->after('system_connect_url');
            $table->boolean('system_connect_enabled')->default(false)->after('system_connect_key_encrypted');
        });

        // One-time backfill: copy account-level credentials onto existing stores
        // so order push keeps working until each store is configured separately.
        $users = DB::table('users')
            ->where('external_api_enabled', true)
            ->whereNotNull('external_api_url')
            ->whereNotNull('external_api_key_encrypted')
            ->get(['id', 'external_api_url', 'external_api_key_encrypted', 'external_api_enabled']);

        foreach ($users as $user) {
            DB::table('stores')
                ->where('user_id', $user->id)
                ->whereNull('system_connect_url')
                ->update([
                    'system_connect_url' => $user->external_api_url,
                    'system_connect_key_encrypted' => $user->external_api_key_encrypted,
                    'system_connect_enabled' => (bool) $user->external_api_enabled,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'system_connect_url',
                'system_connect_key_encrypted',
                'system_connect_enabled',
            ]);
        });
    }
};
