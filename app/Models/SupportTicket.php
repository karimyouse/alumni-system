<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'tracking_code',

        'user_id',
        'name',
        'email',

        
        'role',
        'identifier',
        'title',
        'subject',

        'message',
        'status',
        'priority',

        'admin_id',
        'admin_reply',
        'admin_replied_at',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'admin_replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }
}
