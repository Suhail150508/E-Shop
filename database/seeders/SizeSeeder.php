<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run()
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'];
        foreach ($sizes as $size) {
            Size::firstOrCreate([
                'name' => $size,
                'slug' => strtolower($size),
                'is_active' => true
            ]);
        }
    }
}
