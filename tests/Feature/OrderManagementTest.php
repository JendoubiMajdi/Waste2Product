<?php

namespace Tests\Feature;

use App\Models\CollectionPoint;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_orders_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
    }

    public function test_client_can_create_order(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $product = Product::factory()->create(['quantite' => 100]);
        $collectionPoint = CollectionPoint::factory()->create();

        $response = $this->actingAs($user)->post('/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
            'collection_point_id' => $collectionPoint->id,
            'delivery_address' => '123 Test Street',
        ]);

        $this->assertDatabaseHas('orders', [
            'client_id' => $user->id,
            'collection_point_id' => $collectionPoint->id,
        ]);
    }

    public function test_client_can_view_their_orders(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        Order::factory()->count(3)->create(['client_id' => $client->id]);

        $response = $this->actingAs($client)->get('/my-orders');

        $response->assertStatus(200);
    }

    public function test_transporter_can_view_assigned_orders(): void
    {
        $transporter = User::factory()->create(['role' => 'transporter']);
        Order::factory()->count(2)->create(['transporter_id' => $transporter->id]);

        $response = $this->actingAs($transporter)->get('/orders');

        $response->assertStatus(200);
    }

    public function test_transporter_can_update_order_status(): void
    {
        $transporter = User::factory()->create(['role' => 'transporter']);
        $order = Order::factory()->create([
            'transporter_id' => $transporter->id,
            'status' => 'processing',
        ]);

        $response = $this->actingAs($transporter)->patch("/orders/{$order->id}", [
            'status' => 'delivered',
        ]);

        $this->assertEquals('delivered', $order->fresh()->status);
    }

    public function test_guest_cannot_access_orders_page(): void
    {
        $response = $this->get('/orders');

        $response->assertRedirect('/login');
    }

    public function test_order_total_is_calculated_correctly(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $product1 = Product::factory()->create(['price' => 10.00, 'quantite' => 100]);
        $product2 = Product::factory()->create(['price' => 15.50, 'quantite' => 100]);
        $collectionPoint = CollectionPoint::factory()->create();

        $response = $this->actingAs($user)->post('/orders', [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2], // 20.00
                ['product_id' => $product2->id, 'quantity' => 3], // 46.50
            ],
            'collection_point_id' => $collectionPoint->id,
            'delivery_address' => '123 Test Street',
        ]);

        $order = Order::where('client_id', $user->id)->first();
        $expectedTotal = (10.00 * 2) + (15.50 * 3);

        $this->assertEquals($expectedTotal, $order->total);
    }
}
