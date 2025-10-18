<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_products_page(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
    }

    public function test_products_are_displayed_on_products_page(): void
    {
        $product = Product::factory()->create([
            'name' => 'Recycled Product',
            'price' => 25.99,
        ]);

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Recycled Product');
    }

    public function test_user_can_view_product_details(): void
    {
        $product = Product::factory()->create();

        $response = $this->get("/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/products', [
            'name' => 'New Recycled Item',
            'description' => 'Made from recycled materials',
            'price' => 29.99,
            'quantite' => 50,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'New Recycled Item',
            'price' => 29.99,
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['price' => 20.00]);

        $response = $this->actingAs($admin)->patch("/products/{$product->id}", [
            'price' => 25.00,
        ]);

        $this->assertEquals(25.00, $product->fresh()->price);
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete("/products/{$product->id}");

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_quantity_decreases_after_order(): void
    {
        $product = Product::factory()->create(['quantite' => 100]);

        $product->decrement('quantite', 5);

        $this->assertEquals(95, $product->fresh()->quantite);
    }
}
