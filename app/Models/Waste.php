<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'quantite',
        'dateDepot',
        'localisation',
        'user_id',
        'collection_point_id',
        'image',
        'ai_confidence',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function collectionPoint()
    {
        return $this->belongsTo(CollectionPoint::class);
    }
}
