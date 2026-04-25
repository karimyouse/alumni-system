<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'academic_id',
        'profile_photo',
        'allow_multiple_sessions',
        'password_changed_at',

        
        'last_login_at',
        'is_suspended',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'last_login_at' => 'datetime',
            'allow_multiple_sessions' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    public function allowsMultipleSessions(): bool
    {
        return (bool) ($this->allow_multiple_sessions ?? false);
    }

    public function alumniProfile()
    {
        return $this->hasOne(\App\Models\AlumniProfile::class, 'user_id');
    }

    public function companyProfile()
    {
        return $this->hasOne(\App\Models\CompanyProfile::class, 'user_id');
    }
}
