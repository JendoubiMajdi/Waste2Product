<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChallengeSubmission>
 */
class ChallengeSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'challenge_id' => Challenge::factory(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'proof_image' => 'proof_' . fake()->numberBetween(1, 1000) . '.jpg',
            'submitted_at' => now(),
        ];
    }
}
