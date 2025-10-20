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
        'ai_sentiment_summary',
        'ai_sentiment_score',
        'ai_insights',
        'ai_analyzed_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'max_participants' => 'integer',
        'ai_sentiment_score' => 'decimal:2',
        'ai_analyzed_at' => 'datetime',
    ];

    /**
     * Get the full datetime of the event
     */
    public function getEventDateTimeAttribute()
    {
        // Combine date and time without timezone conversion
        return \Carbon\Carbon::parse($this->event_date->format('Y-m-d') . ' ' . $this->event_time);
    }
    
    /**
     * Get the event date with time for datetime-local input
     */
    public function getEventDateTimeLocalAttribute()
    {
        // Return the exact date and time without timezone conversion
        return $this->event_date->format('Y-m-d') . 'T' . substr($this->event_time, 0, 5);
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

    /**
     * Get all feedback for this event
     */
    public function feedback()
    {
        return $this->hasMany(EventFeedback::class);
    }

    /**
     * Get average rating for this event
     */
    public function averageRating()
    {
        return $this->feedback()->avg('rating');
    }

    /**
     * Check if user has given feedback for this event
     */
    public function hasUserFeedback($userId)
    {
        return $this->feedback()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's feedback for this event
     */
    public function getUserFeedback($userId)
    {
        return $this->feedback()->where('user_id', $userId)->first();
    }

    /**
     * Check if event has ended
     */
    public function hasEnded()
    {
        return \Carbon\Carbon::now()->greaterThan($this->event_date_time);
    }
}
