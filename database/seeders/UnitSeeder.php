<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'slug' => 'piece'],
            ['name' => 'Kg', 'slug' => 'kg'],
            ['name' => 'Liter', 'slug' => 'liter'],
            ['name' => 'Pack', 'slug' => 'pack'],
            ['name' => 'Set', 'slug' => 'set'],
            ['name' => 'Dozen', 'slug' => 'dozen'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['slug' => $unit['slug']],
                ['name' => $unit['name'], 'is_active' => true]
            );
        }
    }
}
