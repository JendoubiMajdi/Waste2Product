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
        'livreur_id',
        'delivery_proof_photo',
        'delivery_signature',
        'delivery_notes',
        'proof_uploaded_at',
        'client_confirmed',
        'client_confirmed_at',
        'client_confirmation_notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'idOrder');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'idClient');
    }

    public function livreur()
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }
}
