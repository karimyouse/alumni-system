<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workshop extends Model
{
    protected $fillable = [
        'organizer_user_id',
        'organizer_role',
        'title',
        'date',
        'time',
        'location',
        'spots',
        'max_spots',
        'status',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WorkshopRegistration::class);
    }
}
