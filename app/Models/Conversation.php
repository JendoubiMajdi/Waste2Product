<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id'];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    // Get the other user in the conversation
    public function getOtherUser($userId)
    {
        return $this->user_one_id == $userId ? $this->userTwo : $this->userOne;
    }
}
