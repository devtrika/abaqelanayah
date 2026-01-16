<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing brands
        DB::table('brands')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert brands
        DB::table('brands')->insert([
            [
                'id' => 1,
                'name' => json_encode(['en' => 'Fresh Farm', 'ar' => 'المزرعة الطازجة'], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => json_encode(['en' => 'Organic Valley', 'ar' => 'الوادي العضوي'], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => json_encode(['en' => 'Green Fields', 'ar' => 'الحقول الخضراء'], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => json_encode(['en' => 'Nature\'s Best', 'ar' => 'أفضل الطبيعة'], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => json_encode(['en' => 'Premium Quality', 'ar' => 'الجودة الممتازة'], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $this->command->info('✅ Brands seeded successfully!');
    }
}
