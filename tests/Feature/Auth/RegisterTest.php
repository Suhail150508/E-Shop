<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_guest_can_register_and_is_redirected_to_home(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'Str0ngP@ssw0rd!2024Test',
            'password_confirmation' => 'Str0ngP@ssw0rd!2024Test',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->assertAuthenticated();
    }
}
