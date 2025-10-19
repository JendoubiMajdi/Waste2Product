<?php

namespace App\Observers;

use App\Models\Product;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class ProductObserver
{
    public function updated(Product $product): void
    {
        // If quantite changed and now is below or equal to threshold, send alert
        if ($product->isDirty('quantite')) {
            $quantity = (int) $product->quantite;
            $threshold = (int) $product->stock_threshold;

            if ($quantity <= $threshold) {
                // notify admins
                $admins = User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new LowStockAlert($product, $quantity));
                }
            }
        }
    }
}
