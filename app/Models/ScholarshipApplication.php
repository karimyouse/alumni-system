<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipApplication extends Model
{
    protected $fillable = [
        'scholarship_id',
        'alumni_user_id',
        'status',
        'applied_date',
    ];

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alumni_user_id');
    }
}
