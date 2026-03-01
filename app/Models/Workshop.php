<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    // ✅ الحل الأساسي لمشكلة NULL (يسمح بحفظ كل الأعمدة بدون ما نضيفها وحدة وحدة)
    protected $guarded = [];

    public function registrations()
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    // ✅ مهم لأن عندك أعمدة organizer_user_id / organizer_role
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }
}
