<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

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

    public static function enabled(string $key, bool $default = true): bool
    {
        try {
            if (!Schema::hasTable('system_settings') || !Schema::hasColumn('system_settings', $key)) {
                return $default;
            }

            $settings = Cache::remember('system_settings_v1', 60, function () {
                return self::query()->first();
            });

            if (!$settings || is_null($settings->{$key})) {
                return $default;
            }

            return (bool) $settings->{$key};
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
