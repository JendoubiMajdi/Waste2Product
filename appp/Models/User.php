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
}
