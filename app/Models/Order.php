<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'date',
        'statut',
        'client_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantite')
            ->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
