<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_challenges_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/challenges');

        $response->assertStatus(200);
    }

    public function test_challenges_are_displayed_on_challenges_page(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create([
            'title' => 'Monthly Recycling Challenge',
            'points' => 100,
        ]);

        $response = $this->actingAs($user)->get('/challenges');

        $response->assertStatus(200);
        $response->assertSee('Monthly Recycling Challenge');
    }

    public function test_user_can_view_challenge_details(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create();

        $response = $this->actingAs($user)->get("/challenges/{$challenge->id}");

        $response->assertStatus(200);
        $response->assertSee($challenge->title);
    }

    public function test_user_can_participate_in_challenge(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create();

        $response = $this->actingAs($user)->post("/challenges/{$challenge->id}/participate");

        $this->assertDatabaseHas('challenge_submissions', [
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
        ]);
    }

    public function test_admin_can_create_challenge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/challenges', [
            'title' => 'New Challenge',
            'description' => 'Test challenge',
            'points' => 200,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $this->assertDatabaseHas('challenges', [
            'title' => 'New Challenge',
            'points' => 200,
        ]);
    }

    public function test_guest_cannot_access_challenges_page(): void
    {
        $response = $this->get('/challenges');

        $response->assertRedirect('/login');
    }
}
