<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Waste>
 */
class WasteFactory extends Factory
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
            'type' => fake()->randomElement(['Plastic', 'Paper', 'Glass', 'Metal', 'Organic']),
            'weight' => fake()->randomFloat(2, 1, 100),
            'image' => 'waste_' . fake()->numberBetween(1, 1000) . '.jpg',
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'description' => fake()->sentence(),
        ];
    }
}
