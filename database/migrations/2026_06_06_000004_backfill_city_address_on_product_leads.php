<?php

use App\Models\ProductLead;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ProductLead::query()
            ->whereNotNull('custom_fields')
            ->chunkById(100, function ($leads) {
                foreach ($leads as $lead) {
                    $customFields = $lead->custom_fields ?? [];
                    $updates = [];

                    if (empty($lead->getRawOriginal('city'))) {
                        $city = $customFields['city'] ?? $customFields['ville'] ?? null;
                        if ($city) {
                            $updates['city'] = $city;
                        }
                    }

                    if (empty($lead->getRawOriginal('address'))) {
                        $address = $customFields['address'] ?? $customFields['adresse'] ?? null;
                        if ($address) {
                            $updates['address'] = $address;
                        }
                    }

                    if (!empty($updates)) {
                        $lead->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        // No rollback needed for data backfill
    }
};
