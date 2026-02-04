<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::updateOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'direction' => 'ltr',
                'is_default' => true,
                'status' => true,
            ]
        );

        Language::updateOrCreate(
            ['code' => 'bn'],
            [
                'name' => 'Bengali',
                'direction' => 'ltr',
                'is_default' => false,
                'status' => true,
            ]
        );

        Language::updateOrCreate(
            ['code' => 'ar'],
            [
                'name' => 'Arabic',
                'direction' => 'rtl',
                'is_default' => false,
                'status' => true,
            ]
        );
    }
}
