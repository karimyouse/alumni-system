<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopRegistration extends Model
{
    protected $fillable = [
        'workshop_id',
        'alumni_user_id',
        'status',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alumni_user_id');
    }
}
