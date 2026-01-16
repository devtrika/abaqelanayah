<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('ar_SA');
        $fakerEn = Faker::create('en_US');

        // Services offered by providers

        DB::table('services')->insert([
            [
                'provider_id' => 1,
                'category_id' => 1, // Hair services
                'name' => json_encode([
                    'en' => 'Hair Cut & Style',
                    'ar' => 'قص وتصفيف الشعر'
                ]),
                'description' => json_encode([
                    'en' => 'Professional hair cutting and styling service with consultation',
                    'ar' => 'خدمة قص وتصفيف الشعر الاحترافية مع الاستشارة'
                ]),
                'price' => 25.00,
                'duration' => 60, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'provider_id' => 1,
                'category_id' => 1, // Hair services
                'name' => json_encode([
                    'en' => 'Hair Coloring',
                    'ar' => 'صبغ الشعر'
                ]),
                'description' => json_encode([
                    'en' => 'Professional hair coloring with premium products',
                    'ar' => 'صبغ الشعر الاحترافي مع منتجات عالية الجودة'
                ]),
                'price' => 45.00,
                'duration' => 120, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'provider_id' => 2,
                'category_id' => 2, // Nail services
                'name' => json_encode([
                    'en' => 'Manicure',
                    'ar' => 'مانيكير'
                ]),
                'description' => json_encode([
                    'en' => 'Complete manicure service with nail art options',
                    'ar' => 'خدمة مانيكير كاملة مع خيارات فن الأظافر'
                ]),
                'price' => 15.00,
                'duration' => 45, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'provider_id' => 2,
                'category_id' => 2, // Nail services
                'name' => json_encode([
                    'en' => 'Pedicure',
                    'ar' => 'بديكير'
                ]),
                'description' => json_encode([
                    'en' => 'Relaxing pedicure with foot massage',
                    'ar' => 'بديكير مريح مع تدليك القدمين'
                ]),
                'price' => 20.00,
                'duration' => 60, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'provider_id' => 3,
                'category_id' => 3, // Skincare
                'name' => json_encode([
                    'en' => 'Facial Treatment',
                    'ar' => 'علاج الوجه'
                ]),
                'description' => json_encode([
                    'en' => 'Deep cleansing facial with moisturizing treatment',
                    'ar' => 'تنظيف عميق للوجه مع علاج الترطيب'
                ]),
                'price' => 35.00,
                'duration' => 90, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'provider_id' => 3,
                'category_id' => 4, // Massage
                'name' => json_encode([
                    'en' => 'Relaxing Massage',
                    'ar' => 'تدليك مريح'
                ]),
                'description' => json_encode([
                    'en' => 'Full body relaxing massage with aromatherapy',
                    'ar' => 'تدليك مريح للجسم كاملاً مع العلاج بالروائح'
                ]),
                'price' => 50.00,
                'duration' => 75, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'provider_id' => 4,
                'category_id' => 5, // Makeup
                'name' => json_encode([
                    'en' => 'Bridal Makeup',
                    'ar' => 'مكياج العروس'
                ]),
                'description' => json_encode([
                    'en' => 'Complete bridal makeup with trial session',
                    'ar' => 'مكياج عروس كامل مع جلسة تجريبية'
                ]),
                'price' => 80.00,
                'duration' => 120, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'provider_id' => 4,
                'category_id' => 5, // Makeup
                'name' => json_encode([
                    'en' => 'Party Makeup',
                    'ar' => 'مكياج الحفلات'
                ]),
                'description' => json_encode([
                    'en' => 'Glamorous makeup for special occasions',
                    'ar' => 'مكياج فخم للمناسبات الخاصة'
                ]),
                'price' => 40.00,
                'duration' => 60, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'provider_id' => 5,
                'category_id' => 6, // Eyebrow
                'name' => json_encode([
                    'en' => 'Eyebrow Threading',
                    'ar' => 'خيط الحواجب'
                ]),
                'description' => json_encode([
                    'en' => 'Precise eyebrow shaping with threading technique',
                    'ar' => 'تشكيل دقيق للحواجب بتقنية الخيط'
                ]),
                'price' => 10.00,
                'duration' => 30, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'provider_id' => 5,
                'category_id' => 6, // Eyebrow
                'name' => json_encode([
                    'en' => 'Eyebrow Tinting',
                    'ar' => 'صبغ الحواجب'
                ]),
                'description' => json_encode([
                    'en' => 'Eyebrow tinting with semi-permanent color',
                    'ar' => 'صبغ الحواجب بلون شبه دائم'
                ]),
                'price' => 15.00,
                'duration' => 45, // minutes
                'is_active' => true,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
        ]);

        $this->command->info('Services seeded successfully!');
    }
}
