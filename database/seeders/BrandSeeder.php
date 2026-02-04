<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Luxe Threads', 'slug' => 'luxe-threads'],
            ['name' => 'Urban Vogue', 'slug' => 'urban-vogue'],
            ['name' => 'Classic Fit', 'slug' => 'classic-fit'],
        ];

        foreach ($brands as $brandData) {
            Brand::updateOrCreate(
                ['slug' => $brandData['slug']],
                ['name' => $brandData['name'], 'is_active' => true]
            );
        }
    }
}
