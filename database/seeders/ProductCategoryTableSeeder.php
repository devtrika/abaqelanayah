<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing categories
        DB::table('categories')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================
        // PARENT CATEGORIES (parent_id = null)
        // ========================================

        // 1. Fruits (Parent Category)
        DB::table('categories')->insert([
            'id' => 1,
            'name' => json_encode(['en' => 'Fruits', 'ar' => 'فواكه'], JSON_UNESCAPED_UNICODE),
            'slug' => Str::slug('Fruits'),
            'parent_id' => null,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Eggs (Parent Category)
        DB::table('categories')->insert([
            'id' => 2,
            'name' => json_encode(['en' => 'Eggs', 'ar' => 'بيض'], JSON_UNESCAPED_UNICODE),
            'slug' => Str::slug('Eggs'),
            'parent_id' => null,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Vegetables (Parent Category)
        DB::table('categories')->insert([
            'id' => 3,
            'name' => json_encode(['en' => 'Vegetables', 'ar' => 'خضروات'], JSON_UNESCAPED_UNICODE),
            'slug' => Str::slug('Vegetables'),
            'parent_id' => null,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 4. Dairy Products (Parent Category)
        DB::table('categories')->insert([
            'id' => 4,
            'name' => json_encode(['en' => 'Dairy Products', 'ar' => 'منتجات الألبان'], JSON_UNESCAPED_UNICODE),
            'slug' => Str::slug('Dairy Products'),
            'parent_id' => null,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // ========================================
        // SUBCATEGORIES (with parent_id)
        // ========================================

        // Fruits Subcategories (parent_id = 1)
        DB::table('categories')->insert([
            [
                'id' => 5,
                'name' => json_encode(['en' => 'Apples', 'ar' => 'تفاح'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Apples'),
                'parent_id' => 1,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' => json_encode(['en' => 'Bananas', 'ar' => 'موز'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Bananas'),
                'parent_id' => 1,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'name' => json_encode(['en' => 'Oranges', 'ar' => 'برتقال'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Oranges'),
                'parent_id' => 1,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'name' => json_encode(['en' => 'Strawberries', 'ar' => 'فراولة'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Strawberries'),
                'parent_id' => 1,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'name' => json_encode(['en' => 'Grapes', 'ar' => 'عنب'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Grapes'),
                'parent_id' => 1,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Eggs Subcategories (parent_id = 2)
        DB::table('categories')->insert([
            [
                'id' => 10,
                'name' => json_encode(['en' => 'Chicken Eggs', 'ar' => 'بيض دجاج'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Chicken Eggs'),
                'parent_id' => 2,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 11,
                'name' => json_encode(['en' => 'Duck Eggs', 'ar' => 'بيض بط'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Duck Eggs'),
                'parent_id' => 2,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 12,
                'name' => json_encode(['en' => 'Quail Eggs', 'ar' => 'بيض سمان'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Quail Eggs'),
                'parent_id' => 2,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Vegetables Subcategories (parent_id = 3)
        DB::table('categories')->insert([
            [
                'id' => 13,
                'name' => json_encode(['en' => 'Tomatoes', 'ar' => 'طماطم'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Tomatoes'),
                'parent_id' => 3,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 14,
                'name' => json_encode(['en' => 'Cucumbers', 'ar' => 'خيار'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Cucumbers'),
                'parent_id' => 3,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Dairy Subcategories (parent_id = 4)
        DB::table('categories')->insert([
            [
                'id' => 15,
                'name' => json_encode(['en' => 'Milk', 'ar' => 'حليب'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Milk'),
                'parent_id' => 4,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 16,
                'name' => json_encode(['en' => 'Cheese', 'ar' => 'جبن'], JSON_UNESCAPED_UNICODE),
                'slug' => Str::slug('Cheese'),
                'parent_id' => 4,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $this->command->info('✅ Categories seeded successfully with parent-child relationships!');
    }
}
