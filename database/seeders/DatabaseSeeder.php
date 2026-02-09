<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            HomePageSeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            UnitSeeder::class,
            ColorSeeder::class,
            SizeSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            RefundReasonSeeder::class,
            SupportDepartmentSeeder::class,
            PageSeeder::class,
        ]);

        // Clear caches so admin website setup and frontend show fresh data after seed
        Cache::forget('settings_all');
        Cache::forget('home_products');
        Cache::forget('featured_categories');
        Cache::forget('featured_categories_8');
        Cache::forget('categories_tree');
    }
}
