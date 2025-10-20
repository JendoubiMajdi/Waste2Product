<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    protected $fillable = ['user_id', 'banned_by', 'reason', 'banned_until'];

    protected $casts = [
        'banned_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    // Check if ban is still active
    public function isActive()
    {
        return $this->banned_until->isFuture();
    }

    // Get days remaining
    public function daysRemaining()
    {
        return now()->diffInDays($this->banned_until, false);
    }
}
