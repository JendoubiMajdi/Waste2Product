<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Waste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WasteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_wastes_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/wastes');

        $response->assertStatus(200);
    }

    public function test_user_can_create_waste_submission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/wastes', [
            'type' => 'Plastic',
            'weight' => 10.5,
            'description' => 'Plastic bottles',
        ]);

        $this->assertDatabaseHas('wastes', [
            'user_id' => $user->id,
            'type' => 'Plastic',
            'weight' => 10.5,
        ]);
    }

    public function test_user_can_view_their_waste_submissions(): void
    {
        $user = User::factory()->create();
        Waste::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/wastes');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_wastes_page(): void
    {
        $response = $this->get('/wastes');

        $response->assertRedirect('/login');
    }

    public function test_waste_requires_type_and_weight(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/wastes', [
            'description' => 'Test waste',
        ]);

        $response->assertSessionHasErrors(['type', 'weight']);
    }
}
