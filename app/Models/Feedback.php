<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // Explicitly set the table name
    protected $table = 'feedbacks';

    protected $fillable = ['user_id', 'note', 'commentaire', 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
