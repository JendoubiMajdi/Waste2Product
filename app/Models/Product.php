<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'waste_id',
        'nom',
        'description',
        'etat',
        'prix',
        'quantite',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'quantite' => 'integer',
    ];

    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantite', 'unit_price', 'subtotal')
            ->withTimestamps();
    }

    /**
     * Decrease product stock
     */
    public function decreaseStock(int $quantity)
    {
        if ($this->quantite < $quantity) {
            throw new \Exception("Insufficient stock for product: {$this->nom}. Available: {$this->quantite}, Required: {$quantity}");
        }

        $this->quantite -= $quantity;
        $this->save();

        return $this;
    }

    /**
     * Increase product stock (e.g., when order is cancelled)
     */
    public function increaseStock(int $quantity)
    {
        $this->quantite += $quantity;
        $this->save();

        return $this;
    }

    /**
     * Check if product has sufficient stock
     */
    public function hasStock(int $quantity): bool
    {
        return $this->quantite >= $quantity;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrixAttribute()
    {
        return number_format((float)$this->prix, 2) . ' DT';
    }
}
