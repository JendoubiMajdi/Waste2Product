<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id', 
        'sender_id', 
        'receiver_id', 
        'message', 
        'shared_post_id', 
        'read_at',
        'voice_file_path',
        'voice_transcription',
        'voice_tone',
        'voice_duration'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sharedPost()
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    // Mark message as read
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}
