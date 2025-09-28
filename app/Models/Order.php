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
        return $this->hasMany(Product::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
