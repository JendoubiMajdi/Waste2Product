<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LowStockAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_receive_low_stock_notification_when_product_below_threshold()
    {
        Notification::fake();

        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a product with quantity above threshold
        $product = Product::factory()->create([
            'quantite' => 20,
            'stock_threshold' => 10,
        ]);

        // Update product quantity below threshold
        $product->quantite = 5;
        $product->save();

        // Assert notification was sent to the admin
        Notification::assertSentTo(
            [$admin],
            LowStockAlert::class,
            function ($notification, $channels) use ($product) {
                return $notification->product->id === $product->id;
            }
        );
    }
}
