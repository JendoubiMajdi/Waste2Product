<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'picture',
        'description',
        'date',
        'time',
        'meet_link',
        'user_id',
    ];


    public function participants()
    {
        return $this->belongsToMany(\App\Models\User::class, 'event_user');
    }

    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
