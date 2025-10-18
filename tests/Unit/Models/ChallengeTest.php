<?php

namespace Tests\Unit\Models;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeTest extends TestCase
{
    use RefreshDatabase;

    public function test_challenge_can_be_created_with_valid_data(): void
    {
        $challenge = Challenge::create([
            'title' => 'Recycle 100kg Challenge',
            'description' => 'Recycle 100kg of waste this month',
            'points' => 500,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $this->assertDatabaseHas('challenges', [
            'title' => 'Recycle 100kg Challenge',
            'points' => 500,
        ]);
    }

    public function test_challenge_has_start_and_end_dates(): void
    {
        $startDate = now();
        $endDate = now()->addMonth();

        $challenge = Challenge::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->assertEquals($startDate->format('Y-m-d'), $challenge->start_date->format('Y-m-d'));
        $this->assertEquals($endDate->format('Y-m-d'), $challenge->end_date->format('Y-m-d'));
    }

    public function test_challenge_has_submissions(): void
    {
        $challenge = Challenge::factory()->hasSubmissions(3)->create();

        $this->assertEquals(3, $challenge->submissions()->count());
    }
}
