<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Media files are typically uploaded by users
        // This seeder creates some sample media records for testing
        
        DB::table('media')->insert([
            [
                'model_type' => 'App\\Models\\Product',
                'model_id' => 1,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'collection_name' => 'default',
                'name' => 'product-image-1',
                'file_name' => 'product-image-1.jpg',
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 1024000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode([]),
                'generated_conversions' => json_encode([]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Service',
                'model_id' => 1,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'collection_name' => 'gallery',
                'name' => 'service-image-1',
                'file_name' => 'service-image-1.png',
                'mime_type' => 'image/png',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 2048000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'Service image']),
                'generated_conversions' => json_encode(['thumb' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Provider',
                'model_id' => 1,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'collection_name' => 'avatar',
                'name' => 'provider-avatar',
                'file_name' => 'provider-avatar.jpg',
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 512000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode([]),
                'generated_conversions' => json_encode(['thumb' => true, 'medium' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Media records seeded successfully!');
    }
}
