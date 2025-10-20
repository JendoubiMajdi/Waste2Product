<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    protected $fillable = ['post_id', 'user_id', 'reason', 'status'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope to get pending reports
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
