<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = '12345678';
        
        // Admin
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Customer
        User::updateOrCreate(
            ['email' => env('CUSTOMER_EMAIL', 'customer@example.com')],
            [
                'name' => env('CUSTOMER_NAME', 'Customer'),
                'password' => Hash::make($password),
                'role' => User::ROLE_CUSTOMER,
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => env('STAFF_EMAIL', 'staff@example.com')],
            [
                'name' => env('STAFF_NAME', 'Staff Member'),
                'password' => Hash::make($password),
                'role' => User::ROLE_STAFF,
            ]
        );
    }
}
