<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductStockNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_email_when_stock_is_low()
    {
        // Intercepter les emails
        Mail::fake();
        
        // Créer un admin
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);

        // Créer un produit
        $product = Product::create([
            'nom' => 'Test Product',
            'description' => 'Test Description',
            'prix' => 100,
            'quantite' => 15,
            'stock_threshold' => 10,
            'waste_id' => 1
        ]);

        // Mettre à jour la quantité en dessous du seuil
        $product->update(['quantite' => 5]);

        // Vérifier que l'email a été envoyé
        Mail::assertSent(function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }
}