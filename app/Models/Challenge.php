<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Challenge
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property int|null $goal
 * @property string|null $reward
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Challenge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'challenges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'goal',
        'reward',
        'status',
        'points',
        'image',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'goal' => 'integer',
        'reward' => 'string',
        'status' => 'string',
        'points' => 'integer',
    ];

    /**
     * Get submissions for this challenge
     */
    public function submissions()
    {
        return $this->hasMany(ChallengeSubmission::class);
    }

    // Timestamps are enabled by default (created_at, updated_at)
}
