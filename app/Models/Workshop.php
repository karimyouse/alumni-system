<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function registrations()
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }


    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }
}
