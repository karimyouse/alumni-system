<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'company_user_id',
        'organizer_user_id',
        'organizer_role',
        'title',
        'company_name',
        'location',
        'type',
        'salary',
        'description',
        'posted',
        'status',
        'views',
        'approval_status',
        'approved_at',
        'approved_by',
        'reject_reason',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(\App\Models\JobApplication::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\User::class, 'company_user_id');
    }

    public function organizer()
    {
        return $this->belongsTo(\App\Models\User::class, 'organizer_user_id');
    }
}
