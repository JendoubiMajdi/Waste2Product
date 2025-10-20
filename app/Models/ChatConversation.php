<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'message',
        'response',
        'sender',
        'voice_file_path',
        'voice_transcription',
        'voice_tone',
        'voice_duration'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
