<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'institution_name',
        'primary_color',

        'email_new_user_notifications',
        'email_content_approval_alerts',
        'email_weekly_reports',

        'auto_backup',
        'last_backup_at',

        'require_2fa',
    ];

    protected $casts = [
        'email_new_user_notifications' => 'boolean',
        'email_content_approval_alerts' => 'boolean',
        'email_weekly_reports' => 'boolean',
        'auto_backup' => 'boolean',
        'require_2fa' => 'boolean',
        'last_backup_at' => 'datetime',
    ];
}
