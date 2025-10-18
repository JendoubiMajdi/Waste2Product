<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_valid_data(): void
    {
        $product = Product::create([
            'name' => 'Recycled Plastic Bottle',
            'description' => 'Made from recycled plastic',
            'price' => 15.99,
            'quantite' => 100,
            'image' => 'product.jpg',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Recycled Plastic Bottle',
            'price' => 15.99,
            'quantite' => 100,
        ]);
    }

    public function test_product_quantity_can_be_decreased(): void
    {
        $product = Product::factory()->create(['quantite' => 100]);

        $product->decrement('quantite', 5);

        $this->assertEquals(95, $product->fresh()->quantite);
    }

    public function test_product_price_is_stored_as_decimal(): void
    {
        $product = Product::factory()->create(['price' => 25.50]);

        $this->assertEquals(25.50, $product->price);
    }
}
