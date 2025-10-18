<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeSubmission extends Model
{
    use HasFactory;

    protected $table = 'challenge_submissions';

    protected $fillable = [
        'challenge_id',
        'user_id',
        'proof_image',
        'description',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
