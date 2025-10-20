<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFeedback extends Model
{
    use HasFactory;

    protected $table = 'event_feedback';

    protected $fillable = [
        'event_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the event that owns the feedback
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who created the feedback
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}