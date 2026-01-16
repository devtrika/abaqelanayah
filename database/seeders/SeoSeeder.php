<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SEO settings for different pages
        
        DB::table('seos')->insert([
            [
                'page' => 'home',
                'title_en' => 'Sorriso - Beauty & Wellness Services',
                'title_ar' => 'سوريسو - خدمات الجمال والعافية',
                'description_en' => 'Book beauty and wellness services with top-rated providers in Kuwait. Hair, nails, skincare, and more.',
                'description_ar' => 'احجز خدمات الجمال والعافية مع أفضل مقدمي الخدمات في الكويت. الشعر والأظافر والعناية بالبشرة والمزيد.',
                'keywords_en' => 'beauty, wellness, hair, nails, skincare, Kuwait, booking',
                'keywords_ar' => 'جمال، عافية، شعر، أظافر، عناية بالبشرة، الكويت، حجز',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'services',
                'title_en' => 'Beauty Services - Sorriso',
                'title_ar' => 'خدمات الجمال - سوريسو',
                'description_en' => 'Explore our wide range of beauty and wellness services. Professional providers at your service.',
                'description_ar' => 'استكشف مجموعتنا الواسعة من خدمات الجمال والعافية. مقدمو خدمات محترفون في خدمتك.',
                'keywords_en' => 'beauty services, hair salon, nail art, massage, facial',
                'keywords_ar' => 'خدمات الجمال، صالون شعر، فن الأظافر، تدليك، تنظيف الوجه',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'providers',
                'title_en' => 'Beauty Providers - Sorriso',
                'title_ar' => 'مقدمو خدمات الجمال - سوريسو',
                'description_en' => 'Find and book with certified beauty professionals in your area.',
                'description_ar' => 'ابحث واحجز مع محترفي الجمال المعتمدين في منطقتك.',
                'keywords_en' => 'beauty providers, professionals, certified, booking',
                'keywords_ar' => 'مقدمو خدمات الجمال، محترفون، معتمدون، حجز',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'products',
                'title_en' => 'Beauty Products - Sorriso',
                'title_ar' => 'منتجات الجمال - سوريسو',
                'description_en' => 'Shop premium beauty products from trusted brands and providers.',
                'description_ar' => 'تسوق منتجات الجمال المميزة من العلامات التجارية ومقدمي الخدمات الموثوقين.',
                'keywords_en' => 'beauty products, cosmetics, skincare, haircare, shopping',
                'keywords_ar' => 'منتجات الجمال، مستحضرات التجميل، العناية بالبشرة، العناية بالشعر، تسوق',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'about',
                'title_en' => 'About Us - Sorriso',
                'title_ar' => 'من نحن - سوريسو',
                'description_en' => 'Learn about Sorriso, your trusted platform for beauty and wellness services in Kuwait.',
                'description_ar' => 'تعرف على سوريسو، منصتك الموثوقة لخدمات الجمال والعافية في الكويت.',
                'keywords_en' => 'about sorriso, beauty platform, Kuwait, wellness',
                'keywords_ar' => 'عن سوريسو، منصة الجمال، الكويت، العافية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('SEO data seeded successfully!');
    }
}
