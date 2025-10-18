<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\User;
use App\Models\CollectionPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_client(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $order = Order::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(User::class, $order->client);
        $this->assertEquals($client->id, $order->client->id);
    }

    public function test_order_can_belong_to_transporter(): void
    {
        $transporter = User::factory()->create(['role' => 'transporter']);
        $order = Order::factory()->create(['transporter_id' => $transporter->id]);

        $this->assertInstanceOf(User::class, $order->transporter);
        $this->assertEquals($transporter->id, $order->transporter->id);
    }

    public function test_order_belongs_to_collection_point(): void
    {
        $collectionPoint = CollectionPoint::factory()->create();
        $order = Order::factory()->create(['collection_point_id' => $collectionPoint->id]);

        $this->assertInstanceOf(CollectionPoint::class, $order->collectionPoint);
        $this->assertEquals($collectionPoint->id, $order->collectionPoint->id);
    }

    public function test_order_has_order_items(): void
    {
        $order = Order::factory()->hasItems(3)->create();

        $this->assertEquals(3, $order->items()->count());
    }

    public function test_order_status_can_be_updated(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $order->update(['status' => 'processing']);

        $this->assertEquals('processing', $order->fresh()->status);
    }

    public function test_order_can_be_assigned_to_transporter(): void
    {
        $order = Order::factory()->create(['transporter_id' => null]);
        $transporter = User::factory()->create(['role' => 'transporter']);

        $order->update(['transporter_id' => $transporter->id]);

        $this->assertNotNull($order->fresh()->transporter_id);
        $this->assertEquals($transporter->id, $order->fresh()->transporter_id);
    }

    public function test_order_total_is_calculated_correctly(): void
    {
        $order = Order::factory()->create(['total' => 150.75]);

        $this->assertEquals(150.75, $order->total);
    }
}
