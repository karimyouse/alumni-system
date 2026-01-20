<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'alumni_user_id',
        'status',
        'applied_date',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function alumni()
    {
    return $this->belongsTo(\App\Models\User::class, 'alumni_user_id');
    }
}
