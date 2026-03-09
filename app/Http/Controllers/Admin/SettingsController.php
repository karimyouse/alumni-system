<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;


class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::query()->first();
        if (!$settings) {
            $settings = SystemSetting::create([
                'institution_name' => 'Palestine Technical College',
                'primary_color' => '#2563eb',
                'email_new_user_notifications' => false,
                'email_content_approval_alerts' => false,
                'email_weekly_reports' => false,
                'auto_backup' => true,
                'require_2fa' => false,
            ]);
        }

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = SystemSetting::query()->first();
        if (!$settings) {
            $settings = SystemSetting::create([]);
        }

        $data = $request->validate([
            'institution_name' => ['required','string','max:255'],
            'primary_color' => ['required','string','max:20'],

            'email_new_user_notifications' => ['nullable'],
            'email_content_approval_alerts' => ['nullable'],
            'email_weekly_reports' => ['nullable'],

            'auto_backup' => ['nullable'],
            'require_2fa' => ['nullable'],

            'backup_now' => ['nullable'],
        ]);

        $settings->update([
            'institution_name' => $data['institution_name'],
            'primary_color' => $data['primary_color'],

            'email_new_user_notifications' => $request->boolean('email_new_user_notifications'),
            'email_content_approval_alerts' => $request->boolean('email_content_approval_alerts'),
            'email_weekly_reports' => $request->boolean('email_weekly_reports'),

            'auto_backup' => $request->boolean('auto_backup'),
            'require_2fa' => $request->boolean('require_2fa'),

        ]);
        \Illuminate\Support\Facades\Cache::forget('system_settings_v1');

        
        if ($request->has('backup_now')) {
            $settings->update(['last_backup_at' => now()]);
            return back()->with('toast_success', 'Backup completed successfully.');
        }

        return back()->with('toast_success', 'Settings saved.');
    }
}
