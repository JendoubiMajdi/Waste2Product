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
        'role',
        'points',
        'badge',
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
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get events created by the user
     */
    public function createdEvents()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get events the user has registered for (as a participant)
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withTimestamps();
    }

    /**
     * Get wastes posted by the user
     */
    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    /**
     * Get orders made by the user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get forum posts created by the user
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get comments made by the user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get donations made by the user
     */
    public function donations()
    {
        return $this->hasMany(Don::class);
    }

    /**
     * Get challenge submissions by the user
     */
    public function challengeSubmissions()
    {
        return $this->hasMany(ChallengeSubmission::class);
    }

    /**
     * Add points to user and update badge if needed
     */
    public function addPoints($points)
    {
        $this->points += $points;
        $this->updateBadge();
        $this->save();
    }

    /**
     * Update user badge based on points
     * Badge levels:
     * - Beginner: 0-49 points
     * - Bronze: 50-149 points
     * - Silver: 150-299 points
     * - Gold: 300-599 points
     * - Platinum: 600-999 points
     * - Diamond: 1000+ points
     */
    public function updateBadge()
    {
        $badges = [
            ['name' => 'diamond', 'threshold' => 1000],
            ['name' => 'platinum', 'threshold' => 600],
            ['name' => 'gold', 'threshold' => 300],
            ['name' => 'silver', 'threshold' => 150],
            ['name' => 'bronze', 'threshold' => 50],
            ['name' => 'beginner', 'threshold' => 0],
        ];

        foreach ($badges as $badge) {
            if ($this->points >= $badge['threshold']) {
                $this->badge = $badge['name'];
                break;
            }
        }
    }

    /**
     * Get badge color for display
     */
    public function getBadgeColorAttribute()
    {
        return match($this->badge) {
            'diamond' => 'b9f2ff',
            'platinum' => 'e5e4e2',
            'gold' => 'ffd700',
            'silver' => 'c0c0c0',
            'bronze' => 'cd7f32',
            'beginner' => '6b7280',
            default => '6b7280',
        };
    }

    /**
     * Get badge icon
     */
    public function getBadgeIconAttribute()
    {
        return match($this->badge) {
            'diamond' => 'gem',
            'platinum' => 'star-fill',
            'gold' => 'trophy-fill',
            'silver' => 'award-fill',
            'bronze' => 'shield-fill-check',
            'beginner' => 'patch-check-fill',
            default => 'patch-check-fill',
        };
    }

    /**
     * Get points needed for next badge
     */
    public function getPointsToNextBadgeAttribute()
    {
        $thresholds = [
            'beginner' => 50,
            'bronze' => 150,
            'silver' => 300,
            'gold' => 600,
            'platinum' => 1000,
            'diamond' => null, // Max level
        ];

        $nextThreshold = $thresholds[$this->badge] ?? null;
        
        if ($nextThreshold === null) {
            return 0; // Already at max level
        }

        return $nextThreshold - $this->points;
    }
}