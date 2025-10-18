<?php

namespace Database\Factories;

use App\Models\CollectionPoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory(['role' => 'client']),
            'transporter_id' => null,
            'collection_point_id' => CollectionPoint::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'total' => fake()->randomFloat(2, 20, 1000),
            'delivery_address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'estimated_delivery_time' => now()->addDays(fake()->numberBetween(3, 10)),
        ];
    }

    /**
     * Indicate that the order has items.
     */
    public function hasItems(int $count = 1)
    {
        return $this->has(
            \App\Models\OrderItem::factory()->count($count),
            'items'
        );
    }
}
