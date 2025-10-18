<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $primaryKey = 'idLivraison';

    protected $fillable = [
        'idOrder',
        'idClient',
        'adresseLivraison',
        'dateLivraison',
        'statut',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'idOrder');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'idClient');
    }
}
