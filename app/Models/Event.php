<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'event_date',
        'event_time',
        'image',
        'max_participants',
        'status',
        'user_id',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i:s',
        'max_participants' => 'integer',
    ];

    /**
     * Get the full datetime of the event
     */
    public function getEventDateTimeAttribute()
    {
        return $this->event_date->format('Y-m-d').' '.$this->event_time;
    }

    /**
     * Get the user who created the event
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get participants for the event
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withTimestamps();
    }

    /**
     * Check if a user is registered for this event
     */
    public function isUserRegistered($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Check if event is full
     */
    public function isFull()
    {
        if (! $this->max_participants) {
            return false;
        }

        return $this->participants()->count() >= $this->max_participants;
    }
}
