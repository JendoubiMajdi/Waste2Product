<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is currently banned
     */
    public function isBanned(): bool
    {
        if (! $this->banned_until) {
            return false;
        }

        return now()->lessThan($this->banned_until);
    }

    /**
     * Get badge color based on badge level
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->badge) {
            'diamond' => 'b9f2ff',
            'platinum' => 'e5e4e2',
            'gold' => 'ffd700',
            'silver' => 'c0c0c0',
            'bronze' => 'cd7f32',
            default => '6b7280', // beginner
        };
    }

    /**
     * Get badge icon based on badge level
     */
    public function getBadgeIconAttribute(): string
    {
        return match ($this->badge) {
            'diamond' => 'gem',
            'platinum' => 'star-fill',
            'gold' => 'trophy-fill',
            'silver' => 'award-fill',
            'bronze' => 'shield-fill-check',
            default => 'patch-check-fill', // beginner
        };
    }

    // ========================================
    // Social Networking Methods
    // ========================================

    /**
     * Get user's friends (accepted friendships)
     */
    public function friends()
    {
        $friendIds1 = Friendship::where('user_id', $this->id)
            ->where('status', 'accepted')
            ->pluck('friend_id');

        $friendIds2 = Friendship::where('friend_id', $this->id)
            ->where('status', 'accepted')
            ->pluck('user_id');

        $allFriendIds = $friendIds1->merge($friendIds2)->unique();

        return self::whereIn('id', $allFriendIds)->get();
    }

    /**
     * Get pending friend requests received by this user
     */
    public function pendingFriendRequests()
    {
        return Friendship::where('friend_id', $this->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();
    }

    /**
     * Check if this user is friends with another user
     */
    public function isFriendsWith($userId): bool
    {
        return Friendship::where(function ($query) use ($userId) {
            $query->where('user_id', $this->id)->where('friend_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('friend_id', $this->id);
        })->where('status', 'accepted')->exists();
    }

    /**
     * Check if this user has blocked another user
     */
    public function hasBlocked($userId): bool
    {
        return BlockedUser::where('user_id', $this->id)
            ->where('blocked_user_id', $userId)
            ->exists();
    }

    /**
     * Check if this user is blocked by another user
     */
    public function isBlockedBy($userId): bool
    {
        return BlockedUser::where('user_id', $userId)
            ->where('blocked_user_id', $this->id)
            ->exists();
    }

    /**
     * Get unread messages count
     */
    public function unreadMessagesCount(): int
    {
        return Message::where('receiver_id', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return Notification::where('user_id', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get user's posts
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get user's conversations
     */
    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id)
            ->with(['user1', 'user2', 'lastMessage'])
            ->latest('updated_at')
            ->get();
    }
}
