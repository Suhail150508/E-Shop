<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_currency' => env('APP_CURRENCY', '$'),
            'contact_email' => env('MAIL_FROM_ADDRESS'),
        ];

        foreach ($settings as $key => $value) {
            if ($value === null) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
