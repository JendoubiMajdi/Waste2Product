<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'date',
        'statut',
        'client_id',
        'total_amount',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantite', 'unit_price', 'subtotal')
            ->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Calculate and update the total amount for this order
     */
    public function calculateTotal()
    {
        $total = $this->products()->sum('order_items.subtotal');
        $this->update(['total_amount' => $total]);
        return $total;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return number_format((float)$this->total_amount, 2) . ' DT';
    }
}