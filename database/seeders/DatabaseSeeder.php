<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        // Phase 1: Core settings and basic data
            $this->call(SettingSeeder::class);
            $this->call(RolesTableSeeder::class);
            $this->call(PermissionTableSeeder::class);
            $this->call(SocialTableSeeder::class);
            $this->call(CountryTableSeeder::class);
            $this->call(RegionTableSeeder::class);
            $this->call(CityTableSeeder::class);


            // Phase 2: Content and categories
            $this->call(FqsTableSeeder::class);
            $this->call(IntroTableSeeder::class);
            $this->call(ProductCategoryTableSeeder::class);
            $this->call(BrandTableSeeder::class);
            $this->call(ProductSeeder::class);
            $this->call(CouponTableSeeder::class);
            $this->call(SmsTableSeeder::class);
            // $this->call(CancelReasonSeeder::class);
            // $this->call(DeliveryPeriodSeeder::class);
            $this->call(PaymentMethodSeeder::class);

            // Phase 3: Users and providers
            $this->call(AdminTableSeeder::class);
            $this->call(ManagerSeeder::class);
            $this->call(UserTableSeeder::class);
            $this->call(DistrictsSeeder::class);






    }
}
