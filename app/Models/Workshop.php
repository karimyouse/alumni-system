<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'title',
        'date',
        'time',
        'location',
        'status',
        'company_user_id',
        'proposal_status',
        'capacity',
    ];

    public function registrations()
    {
        return $this->hasMany(\App\Models\WorkshopRegistration::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\User::class, 'company_user_id');
    }
}
