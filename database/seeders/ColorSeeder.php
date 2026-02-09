<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Red', 'code' => '#DC2626'],
            ['name' => 'Blue', 'code' => '#2563EB'],
            ['name' => 'Green', 'code' => '#16A34A'],
            ['name' => 'Black', 'code' => '#171717'],
            ['name' => 'White', 'code' => '#FAFAFA'],
            ['name' => 'Yellow', 'code' => '#EAB308'],
            ['name' => 'Orange', 'code' => '#EA580C'],
            ['name' => 'Pink', 'code' => '#DB2777'],
            ['name' => 'Purple', 'code' => '#7C3AED'],
            ['name' => 'Gray', 'code' => '#6B7280'],
            ['name' => 'Navy', 'code' => '#1E3A8A'],
            ['name' => 'Brown', 'code' => '#78350F'],
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(
                ['name' => $color['name']],
                ['code' => $color['code'], 'is_active' => true]
            );
        }
    }
}
