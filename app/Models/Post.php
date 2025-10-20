<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'content', 'image', 'media_type', 'media_url', 'post_type', 'don_id', 'share_count', 'visibility', 'shared_post_id',
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

    // Post sharing relationships
    public function sharedPost()
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    public function shares()
    {
        return $this->hasMany(Post::class, 'shared_post_id');
    }

    // Post reports
    public function postReports()
    {
        return $this->hasMany(PostReport::class);
    }

    // Scope for public posts or posts visible to user
    public function scopeVisibleTo($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('visibility', 'public')
                ->orWhere(function ($q2) use ($userId) {
                    $q2->where('visibility', 'friends')
                        ->whereHas('user', function ($q3) use ($userId) {
                            $q3->whereHas('sentFriendRequests', function ($q4) use ($userId) {
                                $q4->where('friend_id', $userId)->where('status', 'accepted');
                            })->orWhereHas('receivedFriendRequests', function ($q4) use ($userId) {
                                $q4->where('user_id', $userId)->where('status', 'accepted');
                            });
                        });
                })
                ->orWhere('user_id', $userId); // User's own posts
        });
    }
}
