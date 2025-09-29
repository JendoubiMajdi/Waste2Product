<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionPoint extends Model
{
    protected $table = 'collection_points';

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'working_hours',
        'contact_phone',
        'status',
        'user_id',
        'image',
    ];
    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }
}
