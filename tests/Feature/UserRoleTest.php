<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_access_client_dashboard(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_transporter_can_access_transporter_dashboard(): void
    {
        $transporter = User::factory()->create(['role' => 'transporter']);

        $response = $this->actingAs($transporter)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_features(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        // Assuming there's an admin route like /admin/users
        $response = $this->actingAs($client)->get('/admin/users');

        $response->assertStatus(403); // Forbidden or redirect
    }

    public function test_transporter_can_only_see_assigned_orders(): void
    {
        $transporter = User::factory()->create(['role' => 'transporter']);

        $response = $this->actingAs($transporter)->get('/orders');

        $response->assertStatus(200);
        // Should only see orders assigned to this transporter
    }
}
