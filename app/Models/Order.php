<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'date',
        'statut',
        'client_id',
        'delivery_address',
        'transporter_id',
        'collection_point_id',
        'estimated_delivery_time',
    ];

    protected $casts = [
        'estimated_delivery_time' => 'datetime',
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

    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    public function collectionPoint()
    {
        return $this->belongsTo(CollectionPoint::class, 'collection_point_id');
    }
}
