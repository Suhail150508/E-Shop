<?php

namespace Database\Seeders;

use App\Models\SupportDepartment;
use Illuminate\Database\Seeder;

class SupportDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'General Inquiry',
            'Sales',
            'Technical Support',
            'Order & Shipping',
            'Returns & Refunds',
        ];

        foreach ($departments as $name) {
            SupportDepartment::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
