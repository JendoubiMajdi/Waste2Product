<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'content', 'image', 'media_type', 'media_url', 'post_type', 'don_id', 'share_count',
    ];

    protected $withCount = ['comments', 'likes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function don()
    {
        return $this->belongsTo(Don::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Like::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }

    public function getUserReactionAttribute()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $reaction = $this->reactions()->where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
            return $reaction ? $reaction->type : null;
        }
        return null;
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}