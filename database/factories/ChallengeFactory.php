<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Challenge>
 */
class ChallengeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'points' => fake()->numberBetween(50, 500),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => fake()->randomElement(['upcoming', 'active', 'completed']),
            'goal' => fake()->sentence(),
        ];
    }

    /**
     * Indicate that the challenge has submissions.
     */
    public function hasSubmissions(int $count = 1)
    {
        return $this->has(
            \App\Models\ChallengeSubmission::factory()->count($count),
            'submissions'
        );
    }
}
