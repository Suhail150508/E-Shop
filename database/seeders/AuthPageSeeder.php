<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class AuthPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Welcome Back!',
                'slug' => 'auth-login',
                'content' => 'Sign in to continue to your account.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Join Us Today',
                'slug' => 'auth-register',
                'content' => 'Create an account to unlock exclusive features.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Forgot Password?',
                'slug' => 'auth-forgot-password',
                'content' => 'Enter your email to reset your password.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Reset Password',
                'slug' => 'auth-reset-password',
                'content' => 'Set a new password for your account.',
                'image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
