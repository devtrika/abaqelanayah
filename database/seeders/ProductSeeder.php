<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing products
        DB::table('products')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================
        // FRUITS PRODUCTS
        // ========================================

        // Apples (category_id = 5, parent_category_id = 1)
        Product::create([
            'name' => ['en' => 'Red Apples', 'ar' => 'تفاح أحمر'],
            'description' => ['en' => 'Fresh red apples from local farms', 'ar' => 'تفاح أحمر طازج من المزارع المحلية'],
            'parent_category_id' => 1, // Fruits
            'category_id' => 5, // Apples
            'brand_id' => 1, // Fresh Farm
            'base_price' => 25.00,
            'discount_percentage' => 10,
            'quantity' => 100,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Red Apples'),
        ]);

        Product::create([
            'name' => ['en' => 'Green Apples', 'ar' => 'تفاح أخضر'],
            'description' => ['en' => 'Crispy green apples', 'ar' => 'تفاح أخضر مقرمش'],
            'parent_category_id' => 1,
            'category_id' => 5,
            'brand_id' => 2, // Organic Valley
            'base_price' => 30.00,
            'discount_percentage' => 0,
            'quantity' => 80,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Green Apples'),
        ]);

        // Bananas (category_id = 6, parent_category_id = 1)
        Product::create([
            'name' => ['en' => 'Yellow Bananas', 'ar' => 'موز أصفر'],
            'description' => ['en' => 'Sweet yellow bananas', 'ar' => 'موز أصفر حلو'],
            'parent_category_id' => 1,
            'category_id' => 6,
            'brand_id' => 1,
            'base_price' => 15.00,
            'discount_percentage' => 5,
            'quantity' => 150,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Yellow Bananas'),
        ]);

        // Oranges (category_id = 7, parent_category_id = 1)
        Product::create([
            'name' => ['en' => 'Fresh Oranges', 'ar' => 'برتقال طازج'],
            'description' => ['en' => 'Juicy fresh oranges', 'ar' => 'برتقال طازج عصيري'],
            'parent_category_id' => 1,
            'category_id' => 7,
            'brand_id' => 3, // Green Fields
            'base_price' => 20.00,
            'discount_percentage' => 15,
            'quantity' => 120,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Fresh Oranges'),
        ]);

        // Strawberries (category_id = 8, parent_category_id = 1)
        Product::create([
            'name' => ['en' => 'Organic Strawberries', 'ar' => 'فراولة عضوية'],
            'description' => ['en' => 'Organic fresh strawberries', 'ar' => 'فراولة طازجة عضوية'],
            'parent_category_id' => 1,
            'category_id' => 8,
            'brand_id' => 2,
            'base_price' => 45.00,
            'discount_percentage' => 20,
            'quantity' => 60,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Organic Strawberries'),
        ]);

        // Grapes (category_id = 9, parent_category_id = 1)
        Product::create([
            'name' => ['en' => 'Red Grapes', 'ar' => 'عنب أحمر'],
            'description' => ['en' => 'Sweet red grapes', 'ar' => 'عنب أحمر حلو'],
            'parent_category_id' => 1,
            'category_id' => 9,
            'brand_id' => 4, // Nature's Best
            'base_price' => 35.00,
            'discount_percentage' => 0,
            'quantity' => 90,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Red Grapes'),
        ]);

        // ========================================
        // EGGS PRODUCTS
        // ========================================

        // Chicken Eggs (category_id = 10, parent_category_id = 2)
        Product::create([
            'name' => ['en' => 'White Chicken Eggs', 'ar' => 'بيض دجاج أبيض'],
            'description' => ['en' => 'Fresh white chicken eggs (12 pieces)', 'ar' => 'بيض دجاج أبيض طازج (12 حبة)'],
            'parent_category_id' => 2, // Eggs
            'category_id' => 10,
            'brand_id' => 1,
            'base_price' => 18.00,
            'discount_percentage' => 0,
            'quantity' => 200,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('White Chicken Eggs'),
        ]);

        Product::create([
            'name' => ['en' => 'Brown Chicken Eggs', 'ar' => 'بيض دجاج بني'],
            'description' => ['en' => 'Organic brown chicken eggs (12 pieces)', 'ar' => 'بيض دجاج بني عضوي (12 حبة)'],
            'parent_category_id' => 2,
            'category_id' => 10,
            'brand_id' => 2,
            'base_price' => 22.00,
            'discount_percentage' => 10,
            'quantity' => 150,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Brown Chicken Eggs'),
        ]);

        // Duck Eggs (category_id = 11, parent_category_id = 2)
        Product::create([
            'name' => ['en' => 'Fresh Duck Eggs', 'ar' => 'بيض بط طازج'],
            'description' => ['en' => 'Premium duck eggs (6 pieces)', 'ar' => 'بيض بط ممتاز (6 حبات)'],
            'parent_category_id' => 2,
            'category_id' => 11,
            'brand_id' => 5, // Premium Quality
            'base_price' => 28.00,
            'discount_percentage' => 0,
            'quantity' => 80,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Fresh Duck Eggs'),
        ]);

        // Quail Eggs (category_id = 12, parent_category_id = 2)
        Product::create([
            'name' => ['en' => 'Quail Eggs', 'ar' => 'بيض سمان'],
            'description' => ['en' => 'Fresh quail eggs (18 pieces)', 'ar' => 'بيض سمان طازج (18 حبة)'],
            'parent_category_id' => 2,
            'category_id' => 12,
            'brand_id' => 5,
            'base_price' => 32.00,
            'discount_percentage' => 5,
            'quantity' => 100,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Quail Eggs'),
        ]);

        // ========================================
        // VEGETABLES PRODUCTS
        // ========================================

        // Tomatoes (category_id = 13, parent_category_id = 3)
        Product::create([
            'name' => ['en' => 'Fresh Tomatoes', 'ar' => 'طماطم طازجة'],
            'description' => ['en' => 'Red fresh tomatoes (1 kg)', 'ar' => 'طماطم حمراء طازجة (1 كجم)'],
            'parent_category_id' => 3, // Vegetables
            'category_id' => 13,
            'brand_id' => 3,
            'base_price' => 12.00,
            'discount_percentage' => 0,
            'quantity' => 180,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Fresh Tomatoes'),
        ]);

        // Cucumbers (category_id = 14, parent_category_id = 3)
        Product::create([
            'name' => ['en' => 'Fresh Cucumbers', 'ar' => 'خيار طازج'],
            'description' => ['en' => 'Green fresh cucumbers (1 kg)', 'ar' => 'خيار أخضر طازج (1 كجم)'],
            'parent_category_id' => 3,
            'category_id' => 14,
            'brand_id' => 3,
            'base_price' => 10.00,
            'discount_percentage' => 0,
            'quantity' => 160,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Fresh Cucumbers'),
        ]);

        // ========================================
        // DAIRY PRODUCTS
        // ========================================

        // Milk (category_id = 15, parent_category_id = 4)
        Product::create([
            'name' => ['en' => 'Fresh Milk', 'ar' => 'حليب طازج'],
            'description' => ['en' => 'Full cream fresh milk (1 liter)', 'ar' => 'حليب طازج كامل الدسم (1 لتر)'],
            'parent_category_id' => 4, // Dairy
            'category_id' => 15,
            'brand_id' => 2,
            'base_price' => 8.00,
            'discount_percentage' => 0,
            'quantity' => 250,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Fresh Milk'),
        ]);

        // Cheese (category_id = 16, parent_category_id = 4)
        Product::create([
            'name' => ['en' => 'Cheddar Cheese', 'ar' => 'جبن شيدر'],
            'description' => ['en' => 'Premium cheddar cheese (500g)', 'ar' => 'جبن شيدر ممتاز (500 جرام)'],
            'parent_category_id' => 4,
            'category_id' => 16,
            'brand_id' => 5,
            'base_price' => 42.00,
            'discount_percentage' => 15,
            'quantity' => 70,
            'is_active' => true,
            'is_refunded' => false,
            'slug' => Str::slug('Cheddar Cheese'),
        ]);

        $this->command->info('✅ Products seeded successfully with proper categories and brands!');
    }
}
