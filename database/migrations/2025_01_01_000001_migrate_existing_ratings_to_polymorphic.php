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
        // Migrate provider ratings
        if (Schema::hasTable('provider_rates')) {
            $providerRates = DB::table('provider_rates')->get();
            
            foreach ($providerRates as $rate) {
                DB::table('rates')->insert([
                    'user_id' => $rate->user_id,
                    'rateable_type' => 'App\\Models\\Provider',
                    'rateable_id' => $rate->provider_id,
                    'rate' => $rate->rate,
                    'body' => $rate->body,
                    'created_at' => $rate->created_at,
                    'updated_at' => $rate->updated_at,
                ]);
            }
        }

        // Migrate service ratings
        if (Schema::hasTable('service_rates')) {
            $serviceRates = DB::table('service_rates')->get();
            
            foreach ($serviceRates as $rate) {
                DB::table('rates')->insert([
                    'user_id' => $rate->user_id,
                    'rateable_type' => 'App\\Models\\Service',
                    'rateable_id' => $rate->service_id,
                    'rate' => $rate->rate,
                    'body' => $rate->body,
                    'created_at' => $rate->created_at,
                    'updated_at' => $rate->updated_at,
                ]);
            }
        }

        // Migrate media files from provider_rates
        if (Schema::hasTable('provider_rates')) {
            $this->migrateMediaFiles('App\\Models\\ProviderRate', 'App\\Models\\Rate');
        }

        // Migrate media files from service_rates  
        if (Schema::hasTable('service_rates')) {
            $this->migrateMediaFiles('App\\Models\\ServiceRate', 'App\\Models\\Rate');
        }
    }

    /**
     * Migrate media files from old models to new Rate model
     */
    private function migrateMediaFiles(string $oldModelType, string $newModelType): void
    {
        $mediaItems = DB::table('media')
            ->where('model_type', $oldModelType)
            ->get();

        foreach ($mediaItems as $media) {
            // Find corresponding rate in new table
            $oldRate = null;
            if ($oldModelType === 'App\\Models\\ProviderRate') {
                $oldRate = DB::table('provider_rates')->find($media->model_id);
                if ($oldRate) {
                    $newRate = DB::table('rates')
                        ->where('user_id', $oldRate->user_id)
                        ->where('rateable_type', 'App\\Models\\Provider')
                        ->where('rateable_id', $oldRate->provider_id)
                        ->first();
                }
            } elseif ($oldModelType === 'App\\Models\\ServiceRate') {
                $oldRate = DB::table('service_rates')->find($media->model_id);
                if ($oldRate) {
                    $newRate = DB::table('rates')
                        ->where('user_id', $oldRate->user_id)
                        ->where('rateable_type', 'App\\Models\\Service')
                        ->where('rateable_id', $oldRate->service_id)
                        ->first();
                }
            }

            if (isset($newRate)) {
                DB::table('media')
                    ->where('id', $media->id)
                    ->update([
                        'model_type' => $newModelType,
                        'model_id' => $newRate->id,
                        'collection_name' => 'rate-media'
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible
        // You would need to recreate the old tables and migrate data back
    }
};
