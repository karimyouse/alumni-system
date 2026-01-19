<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardEntry extends Model
{
    protected $fillable = [
        'alumni_user_id', 'rank', 'points', 'activities', 'trend', 'period'
    ];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alumni_user_id');
    }
}
