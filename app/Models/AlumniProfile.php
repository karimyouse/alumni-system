<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'location',
        'major',
        'graduation_year',
        'gpa',
        'bio',
        'skills',
        'linkedin',
        'portfolio',
        'profile_photo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
