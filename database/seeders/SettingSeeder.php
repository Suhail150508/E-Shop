<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Seed essential settings only. Home hero, section titles, etc. can be set from Admin → Website Setup.
     */
    public function run(): void
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_currency' => env('APP_CURRENCY', '$'),
            'app_logo' => '',
            'app_favicon' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value === null ? '' : $value]
            );
        }
    }
}
