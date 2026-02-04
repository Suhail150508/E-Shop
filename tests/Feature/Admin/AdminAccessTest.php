<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_forbidden_on_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Total Orders');
    }
}
