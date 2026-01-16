<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntroFqsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // FAQ items for the introduction section
        
        DB::table('intro_fqs')->insert([
            [
                'category_id' => 1, // General Questions
                'question_en' => 'What is Sorriso?',
                'question_ar' => 'ما هو سوريسو؟',
                'answer_en' => 'Sorriso is a comprehensive beauty and wellness platform that connects you with certified professionals for various beauty services.',
                'answer_ar' => 'سوريسو هو منصة شاملة للجمال والعافية تربطك بالمحترفين المعتمدين لمختلف خدمات الجمال.',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // General Questions
                'question_en' => 'How does Sorriso work?',
                'question_ar' => 'كيف يعمل سوريسو؟',
                'answer_en' => 'Simply browse our services, select your preferred provider, choose a time slot, and book your appointment. You can also shop for beauty products.',
                'answer_ar' => 'ببساطة تصفح خدماتنا، اختر مقدم الخدمة المفضل لديك، اختر الوقت المناسب، واحجز موعدك. يمكنك أيضاً التسوق لمنتجات الجمال.',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2, // Booking & Appointments
                'question_en' => 'How do I book an appointment?',
                'question_ar' => 'كيف أحجز موعداً؟',
                'answer_en' => 'Select your desired service, choose a provider, pick an available time slot, and confirm your booking with payment.',
                'answer_ar' => 'اختر الخدمة المرغوبة، اختر مقدم الخدمة، اختر الوقت المتاح، وأكد حجزك بالدفع.',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2, // Booking & Appointments
                'question_en' => 'Can I cancel or reschedule my appointment?',
                'question_ar' => 'هل يمكنني إلغاء أو إعادة جدولة موعدي؟',
                'answer_en' => 'Yes, you can cancel or reschedule your appointment up to 24 hours before the scheduled time.',
                'answer_ar' => 'نعم، يمكنك إلغاء أو إعادة جدولة موعدك حتى 24 ساعة قبل الوقت المحدد.',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3, // Payments & Pricing
                'question_en' => 'What payment methods do you accept?',
                'question_ar' => 'ما هي طرق الدفع التي تقبلونها؟',
                'answer_en' => 'We accept credit cards, debit cards, K-Net, and bank transfers.',
                'answer_ar' => 'نقبل بطاقات الائتمان وبطاقات الخصم وكي-نت والتحويلات البنكية.',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4, // Service Providers
                'question_en' => 'Are your service providers certified?',
                'question_ar' => 'هل مقدمو الخدمات لديكم معتمدون؟',
                'answer_en' => 'Yes, all our service providers are certified professionals with verified credentials and experience.',
                'answer_ar' => 'نعم، جميع مقدمي الخدمات لدينا محترفون معتمدون بمؤهلات وخبرة موثقة.',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Intro FAQs seeded successfully!');
    }
}
