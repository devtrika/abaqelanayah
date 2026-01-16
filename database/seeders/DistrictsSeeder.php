<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get Saudi Arabian cities (country_id = 2)
        $saudiCities = DB::table('cities')->where('country_id', 2)->get();
        
        // Saudi districts data organized by major cities with translations
        $districtsData = [
            // Riyadh Districts
            'الرياض' => [
                ['ar' => 'العليا', 'en' => 'Al Olaya'],
                ['ar' => 'الملز', 'en' => 'Al Malaz'],
                ['ar' => 'النخيل', 'en' => 'Al Nakheel'],
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'الصحافة', 'en' => 'Al Sahafah'],
                ['ar' => 'الورود', 'en' => 'Al Wurud'],
                ['ar' => 'الياسمين', 'en' => 'Al Yasmin'],
                ['ar' => 'الفلاح', 'en' => 'Al Falah'],
                ['ar' => 'الربوة', 'en' => 'Al Rabwah'],
                ['ar' => 'المروج', 'en' => 'Al Muruj'],
                ['ar' => 'النرجس', 'en' => 'Al Narjis'],
                ['ar' => 'الواحة', 'en' => 'Al Wahah'],
                ['ar' => 'الحمراء', 'en' => 'Al Hamra'],
                ['ar' => 'الشفا', 'en' => 'Al Shifa'],
                ['ar' => 'الدرعية', 'en' => 'Diriyah'],
                ['ar' => 'العارض', 'en' => 'Al Arid'],
                ['ar' => 'الدوادمي', 'en' => 'Al Dawadmi'],
                ['ar' => 'المزاحمية', 'en' => 'Al Muzahimiyah'],
                ['ar' => 'الخرج', 'en' => 'Al Kharj'],
                ['ar' => 'الأفلاج', 'en' => 'Al Aflaj']
            ],
            
            // Jeddah Districts
            'جدة' => [
                ['ar' => 'البلد', 'en' => 'Al Balad'],
                ['ar' => 'الصفا', 'en' => 'Al Safa'],
                ['ar' => 'المروة', 'en' => 'Al Marwah'],
                ['ar' => 'الكندرة', 'en' => 'Al Kandara'],
                ['ar' => 'الشاطئ', 'en' => 'Al Shati'],
                ['ar' => 'الحمراء', 'en' => 'Al Hamra'],
                ['ar' => 'النزهة', 'en' => 'Al Nuzha'],
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'الزهراء', 'en' => 'Al Zahra'],
                ['ar' => 'الفيصلية', 'en' => 'Al Faisaliyah'],
                ['ar' => 'الأندلس', 'en' => 'Al Andalus'],
                ['ar' => 'المحمدية', 'en' => 'Al Muhammadiyah'],
                ['ar' => 'الرحاب', 'en' => 'Al Rehab'],
                ['ar' => 'أبحر الشمالية', 'en' => 'Abhur North'],
                ['ar' => 'أبحر الجنوبية', 'en' => 'Abhur South'],
                ['ar' => 'ذهبان', 'en' => 'Dhahban'],
                ['ar' => 'الليث', 'en' => 'Al Lith'],
                ['ar' => 'رابغ', 'en' => 'Rabigh']
            ],
            
            // Makkah Districts
            'مكة المكرمة' => [
                ['ar' => 'الحرم', 'en' => 'Al Haram'],
                ['ar' => 'العزيزية', 'en' => 'Al Aziziyah'],
                ['ar' => 'الشوقية', 'en' => 'Al Shawqiyah'],
                ['ar' => 'المسفلة', 'en' => 'Al Misfalah'],
                ['ar' => 'جرول', 'en' => 'Jarwal'],
                ['ar' => 'الكعكية', 'en' => 'Al Kakiyah'],
                ['ar' => 'الطندباوي', 'en' => 'Al Tandabawi'],
                ['ar' => 'الهجلة', 'en' => 'Al Hajlah'],
                ['ar' => 'الراشدية', 'en' => 'Al Rashidiyah'],
                ['ar' => 'الزاهر', 'en' => 'Al Zahir'],
                ['ar' => 'النوارية', 'en' => 'Al Nawariyah'],
                ['ar' => 'الشرائع', 'en' => 'Al Sharai'],
                ['ar' => 'منى', 'en' => 'Mina'],
                ['ar' => 'عرفات', 'en' => 'Arafat'],
                ['ar' => 'مزدلفة', 'en' => 'Muzdalifah'],
                ['ar' => 'التنعيم', 'en' => 'Al Tanim']
            ],
            
            // Medina Districts
            'المدينة المنورة' => [
                ['ar' => 'الحرم النبوي', 'en' => 'Al Haram Al Nabawi'],
                ['ar' => 'قباء', 'en' => 'Quba'],
                ['ar' => 'العوالي', 'en' => 'Al Awali'],
                ['ar' => 'الجرف', 'en' => 'Al Jurf'],
                ['ar' => 'الأزهري', 'en' => 'Al Azhari'],
                ['ar' => 'السيح', 'en' => 'Al Seeh'],
                ['ar' => 'الدويمة', 'en' => 'Al Duwaimah'],
                ['ar' => 'الجماوات', 'en' => 'Al Jamawat'],
                ['ar' => 'الفريش', 'en' => 'Al Fareesh'],
                ['ar' => 'بدر', 'en' => 'Badr'],
                ['ar' => 'خيبر', 'en' => 'Khaybar'],
                ['ar' => 'العلا', 'en' => 'Al Ula'],
                ['ar' => 'ينبع', 'en' => 'Yanbu'],
                ['ar' => 'المهد', 'en' => 'Al Mahd'],
                ['ar' => 'الحناكية', 'en' => 'Al Hanakiyah'],
                ['ar' => 'وادي الفرع', 'en' => 'Wadi Al Far']
            ],
            
            // Dammam Districts
            'الدمام' => [
                ['ar' => 'الفردوس', 'en' => 'Al Firdaws'],
                ['ar' => 'الشاطئ', 'en' => 'Al Shati'],
                ['ar' => 'الجلوية', 'en' => 'Al Jalawiyah'],
                ['ar' => 'الأمانة', 'en' => 'Al Amanah'],
                ['ar' => 'الفيصلية', 'en' => 'Al Faisaliyah'],
                ['ar' => 'النور', 'en' => 'Al Noor'],
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'الصناعية', 'en' => 'Industrial Area'],
                ['ar' => 'الخليج', 'en' => 'Al Khaleej'],
                ['ar' => 'المريكبات', 'en' => 'Al Muraikabat'],
                ['ar' => 'الندى', 'en' => 'Al Nada'],
                ['ar' => 'الواحة', 'en' => 'Al Wahah'],
                ['ar' => 'الجوهرة', 'en' => 'Al Jawharah'],
                ['ar' => 'الصفا', 'en' => 'Al Safa'],
                ['ar' => 'المنار', 'en' => 'Al Manar'],
                ['ar' => 'الضباب', 'en' => 'Al Dabab']
            ],
            
            // Khobar Districts
            'الخبر' => [
                ['ar' => 'الكورنيش', 'en' => 'Corniche'],
                ['ar' => 'الثقبة', 'en' => 'Thuqbah'],
                ['ar' => 'الراكة', 'en' => 'Al Rakah'],
                ['ar' => 'العقربية', 'en' => 'Al Aqrabiyah'],
                ['ar' => 'الجسر', 'en' => 'Al Jisr'],
                ['ar' => 'الحزام الأخضر', 'en' => 'Green Belt'],
                ['ar' => 'الخزامى', 'en' => 'Al Khuzama'],
                ['ar' => 'اليرموك', 'en' => 'Al Yarmouk'],
                ['ar' => 'الصواري', 'en' => 'Al Sawari'],
                ['ar' => 'الدانة', 'en' => 'Al Dana'],
                ['ar' => 'العليا', 'en' => 'Al Olaya'],
                ['ar' => 'الشاطئ الشرقي', 'en' => 'Eastern Beach']
            ],
            
            // Abha Districts
            'ابها' => [
                ['ar' => 'المنهل', 'en' => 'Al Manhal'],
                ['ar' => 'الموظفين', 'en' => 'Al Muwadhafin'],
                ['ar' => 'النسيم', 'en' => 'Al Naseem'],
                ['ar' => 'الضباب', 'en' => 'Al Dabab'],
                ['ar' => 'الأندلس', 'en' => 'Al Andalus'],
                ['ar' => 'الورود', 'en' => 'Al Wurud'],
                ['ar' => 'المطار', 'en' => 'Airport'],
                ['ar' => 'الشفا', 'en' => 'Al Shifa'],
                ['ar' => 'السودة', 'en' => 'Al Souda'],
                ['ar' => 'خميس مشيط', 'en' => 'Khamis Mushait'],
                ['ar' => 'أحد رفيدة', 'en' => 'Ahad Rafidah'],
                ['ar' => 'بيشة', 'en' => 'Bisha'],
                ['ar' => 'النماص', 'en' => 'Al Namas'],
                ['ar' => 'تنومة', 'en' => 'Tanomah'],
                ['ar' => 'محايل عسير', 'en' => 'Muhayil Asir']
            ],
            
            // Tabuk Districts
            'تبوك' => [
                ['ar' => 'الفيصلية', 'en' => 'Al Faisaliyah'],
                ['ar' => 'الورود', 'en' => 'Al Wurud'],
                ['ar' => 'الأمير فهد بن سلطان', 'en' => 'Prince Fahd bin Sultan'],
                ['ar' => 'السليمانية', 'en' => 'Al Sulaymaniyah'],
                ['ar' => 'الصناعية', 'en' => 'Industrial Area'],
                ['ar' => 'المطار', 'en' => 'Airport'],
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'حقل', 'en' => 'Haql'],
                ['ar' => 'ضباء', 'en' => 'Duba'],
                ['ar' => 'الوجه', 'en' => 'Al Wajh'],
                ['ar' => 'أملج', 'en' => 'Umluj'],
                ['ar' => 'تيماء', 'en' => 'Tayma'],
                ['ar' => 'البدع', 'en' => 'Al Bada'],
                ['ar' => 'الخريبة', 'en' => 'Al Khuraybah']
            ],
            
            // Hail Districts
            'حائل' => [
                ['ar' => 'الصناعية', 'en' => 'Industrial Area'],
                ['ar' => 'الياسمين', 'en' => 'Al Yasmin'],
                ['ar' => 'الأندلس', 'en' => 'Al Andalus'],
                ['ar' => 'المطار', 'en' => 'Airport'],
                ['ar' => 'الملك فهد', 'en' => 'King Fahd'],
                ['ar' => 'الفيصلية', 'en' => 'Al Faisaliyah'],
                ['ar' => 'الزبارة', 'en' => 'Al Zabarah'],
                ['ar' => 'بقعاء', 'en' => 'Buqayq'],
                ['ar' => 'الشنان', 'en' => 'Al Shinan'],
                ['ar' => 'الغزالة', 'en' => 'Al Ghazalah'],
                ['ar' => 'موقق', 'en' => 'Mawqaq'],
                ['ar' => 'الحائط', 'en' => 'Al Hait'],
                ['ar' => 'الشملي', 'en' => 'Al Shamli'],
                ['ar' => 'سميراء', 'en' => 'Sumayra']
            ],
            
            // Buraydah Districts
            'بريدة' => [
                ['ar' => 'الصفراء', 'en' => 'Al Safra'],
                ['ar' => 'الفايزية', 'en' => 'Al Fayziyah'],
                ['ar' => 'الإسكان', 'en' => 'Al Iskan'],
                ['ar' => 'الخبيب', 'en' => 'Al Khabib'],
                ['ar' => 'الأندلس', 'en' => 'Al Andalus'],
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'الفيصلية', 'en' => 'Al Faisaliyah'],
                ['ar' => 'المطار', 'en' => 'Airport'],
                ['ar' => 'عنيزة', 'en' => 'Unaizah'],
                ['ar' => 'الرس', 'en' => 'Al Rass'],
                ['ar' => 'المذنب', 'en' => 'Al Mithnab'],
                ['ar' => 'البكيرية', 'en' => 'Al Bukayriyah'],
                ['ar' => 'عيون الجواء', 'en' => 'Uyun Al Jawa'],
                ['ar' => 'الأسياح', 'en' => 'Al Asyah']
            ],
            
            // Jazan Districts
            'جازان' => [
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'الشاطئ', 'en' => 'Al Shati'],
                ['ar' => 'المطار', 'en' => 'Airport'],
                ['ar' => 'الصناعية', 'en' => 'Industrial Area'],
                ['ar' => 'الرونة', 'en' => 'Al Rawnah'],
                ['ar' => 'الموسم', 'en' => 'Al Mawsim'],
                ['ar' => 'صبيا', 'en' => 'Sabya'],
                ['ar' => 'أبو عريش', 'en' => 'Abu Arish'],
                ['ar' => 'صامطة', 'en' => 'Samtah'],
                ['ar' => 'الدائر', 'en' => 'Al Dair'],
                ['ar' => 'الحرث', 'en' => 'Al Harth'],
                ['ar' => 'الطوال', 'en' => 'Al Tuwal'],
                ['ar' => 'العيدابي', 'en' => 'Al Edabi'],
                ['ar' => 'فرسان', 'en' => 'Farasan']
            ],
            
            // Taif Districts
            'الطائف' => [
                ['ar' => 'الحوية', 'en' => 'Al Huwaya'],
                ['ar' => 'الشفا', 'en' => 'Al Shifa'],
                ['ar' => 'الهدا', 'en' => 'Al Hada'],
                ['ar' => 'الروضة', 'en' => 'Al Rawdah'],
                ['ar' => 'الفيصلية', 'en' => 'Al Faisaliyah'],
                ['ar' => 'السلامة', 'en' => 'Al Salamah'],
                ['ar' => 'الوشحاء', 'en' => 'Al Washha'],
                ['ar' => 'الوهط', 'en' => 'Al Waht'],
                ['ar' => 'تربة', 'en' => 'Turbah'],
                ['ar' => 'رنية', 'en' => 'Ranyah'],
                ['ar' => 'الخرمة', 'en' => 'Al Khurmah'],
                ['ar' => 'الموية', 'en' => 'Al Muwayh'],
                ['ar' => 'ميسان', 'en' => 'Misan'],
                ['ar' => 'بني سعد', 'en' => 'Bani Saad']
            ]
        ];
        
        $districts = [];
        
        foreach ($saudiCities as $city) {
            // Check if we have specific districts for this city
            if (isset($districtsData[$city->name])) {
                foreach ($districtsData[$city->name] as $districtData) {
                    $districts[] = [
                        'name' => json_encode(['ar' => $districtData['ar'], 'en' => $districtData['en']]),
                        'city_id' => $city->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            } else {
                // For cities without specific districts, create generic districts
                $genericDistricts = [
                    ['ar' => 'الوسط', 'en' => 'Central'],
                    ['ar' => 'الشمال', 'en' => 'North'],
                    ['ar' => 'الجنوب', 'en' => 'South'],
                    ['ar' => 'الشرق', 'en' => 'East'],
                    ['ar' => 'الغرب', 'en' => 'West'],
                    ['ar' => 'الصناعية', 'en' => 'Industrial Area'],
                    ['ar' => 'السكني', 'en' => 'Residential']
                ];
                
                foreach ($genericDistricts as $districtData) {
                    $districts[] = [
                        'name' => json_encode(['ar' => $districtData['ar'], 'en' => $districtData['en']]),
                        'city_id' => $city->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }
        
        // Insert districts in batches to avoid memory issues
        $chunks = array_chunk($districts, 100);
        foreach ($chunks as $chunk) {
            DB::table('districts')->insert($chunk);
        }
        
        $this->command->info('Districts seeded successfully for Saudi Arabian cities!');
    }
}