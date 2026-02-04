<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'app_name' => 'Test Shop',
                'app_currency' => '$',
                'contact_email' => 'admin@example.com',
            ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('settings', [
            'key' => 'app_name',
            'value' => 'Test Shop',
        ]);
    }
}
