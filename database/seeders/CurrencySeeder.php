<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCode = 'USD';
        $defaultSymbol = env('APP_CURRENCY', '$');

        Currency::updateOrCreate(
            ['code' => $defaultCode],
            [
                'symbol' => $defaultSymbol,
                'rate' => 1,
                'is_default' => true,
                'status' => true,
            ]
        );

        Currency::updateOrCreate(
            ['code' => 'BDT'],
            [
                'symbol' => '৳',
                'rate' => 109.50,
                'is_default' => false,
                'status' => true,
            ]
        );

        Currency::updateOrCreate(
            ['code' => 'EUR'],
            [
                'symbol' => '€',
                'rate' => 0.92,
                'is_default' => false,
                'status' => true,
            ]
        );
    }
}
