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

    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantite')
            ->withTimestamps();
    }
}
