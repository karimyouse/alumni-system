@extends('layouts.dashboard')

@php
  $title = 'System Settings';

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $settings = $settings ?? (object)[
    'institution_name' => 'Palestine Technical College',
    'primary_color' => '#2563eb',
    'email_new_user_notifications' => false,
    'email_content_approval_alerts' => false,
    'email_weekly_reports' => false,
    'auto_backup' => true,
    'require_2fa' => false,
    'last_backup_at' => null,
  ];

  $lastBackupText = $settings->last_backup_at
    ? \Carbon\Carbon::parse($settings->last_backup_at)->format('M d, Y \a\t h:i A')
    : '—';


  $switch = function ($name, $checked) {
    $isOn = old($name, $checked) ? true : false;
    return [
      'isOn' => $isOn,
      'checkedAttr' => $isOn ? 'checked' : '',
    ];
  };
@endphp

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
  @csrf

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">System Settings</h1>
      <p class="text-sm text-muted-foreground">Configure system settings</p>
    </div>

    <button type="submit"
            class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2">
      <i data-lucide="save" class="h-4 w-4"></i>
      Save
    </button>
  </div>


  <div class="rounded-xl border border-border bg-card p-6">
    <div class="text-lg font-semibold mb-1 inline-flex items-center gap-2">
      <i data-lucide="palette" class="h-4 w-4"></i>
      Appearance
    </div>
    <p class="text-sm text-muted-foreground mb-6">Customize the look and feel</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-sm font-medium">Institution Name</div>
        <div class="text-xs text-muted-foreground mb-2">Displayed in the header</div>
        <input name="institution_name"
               value="{{ old('institution_name', $settings->institution_name) }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">
        @error('institution_name') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <div class="text-sm font-medium">Primary Color</div>
        <div class="text-xs text-muted-foreground mb-2">Main theme color</div>

        <div class="flex items-center gap-2">
          <input name="primary_color"
                 value="{{ old('primary_color', $settings->primary_color) }}"
                 class="flex-1 rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">

          <input type="color"
                 value="{{ old('primary_color', $settings->primary_color) }}"
                 class="h-9 w-10 rounded-md border border-border bg-transparent"
                 oninput="document.querySelector('[name=primary_color]').value = this.value;">
        </div>

        @error('primary_color') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>


  <div class="rounded-xl border border-border bg-card p-6">
    <div class="text-lg font-semibold mb-1 inline-flex items-center gap-2">
      <i data-lucide="mail" class="h-4 w-4"></i>
      Email Notifications
    </div>
    <p class="text-sm text-muted-foreground mb-6">Configure email settings</p>

    @php $s1 = $switch('email_new_user_notifications', (bool)$settings->email_new_user_notifications); @endphp
    <div class="flex items-center justify-between py-4 border-t border-border">
      <div>
        <div class="text-sm font-medium">New User Notifications</div>
        <div class="text-xs text-muted-foreground">Email admin when new users register</div>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="email_new_user_notifications" value="1" class="sr-only peer" {{ $s1['checkedAttr'] }}>
        <span class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary transition"></span>
        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-background transition peer-checked:translate-x-5"></span>
      </label>
    </div>

    @php $s2 = $switch('email_content_approval_alerts', (bool)$settings->email_content_approval_alerts); @endphp
    <div class="flex items-center justify-between py-4 border-t border-border">
      <div>
        <div class="text-sm font-medium">Content Approval Alerts</div>
        <div class="text-xs text-muted-foreground">Email when content needs approval</div>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="email_content_approval_alerts" value="1" class="sr-only peer" {{ $s2['checkedAttr'] }}>
        <span class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary transition"></span>
        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-background transition peer-checked:translate-x-5"></span>
      </label>
    </div>

    @php $s3 = $switch('email_weekly_reports', (bool)$settings->email_weekly_reports); @endphp
    <div class="flex items-center justify-between py-4 border-t border-border">
      <div>
        <div class="text-sm font-medium">Weekly Reports</div>
        <div class="text-xs text-muted-foreground">Send weekly summary emails</div>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="email_weekly_reports" value="1" class="sr-only peer" {{ $s3['checkedAttr'] }}>
        <span class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary transition"></span>
        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-background transition peer-checked:translate-x-5"></span>
      </label>
    </div>
  </div>


  <div class="rounded-xl border border-border bg-card p-6">
    <div class="text-lg font-semibold mb-1 inline-flex items-center gap-2">
      <i data-lucide="database" class="h-4 w-4"></i>
      Data Management
    </div>
    <p class="text-sm text-muted-foreground mb-6">Backup and restore options</p>

    <div class="flex items-center justify-between py-4 border-t border-border">
      <div>
        <div class="text-sm font-medium">Last Backup</div>
        <div class="text-xs text-muted-foreground">{{ $lastBackupText }}</div>
      </div>

      <button type="submit" name="backup_now" value="1"
              class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 inline-flex items-center gap-2">
        <i data-lucide="hard-drive" class="h-4 w-4"></i>
        Backup Now
      </button>
    </div>

    @php $s4 = $switch('auto_backup', (bool)$settings->auto_backup); @endphp
    <div class="flex items-center justify-between py-4 border-t border-border">
      <div>
        <div class="text-sm font-medium">Auto Backup</div>
        <div class="text-xs text-muted-foreground">Daily automatic backups</div>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="auto_backup" value="1" class="sr-only peer" {{ $s4['checkedAttr'] }}>
        <span class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary transition"></span>
        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-background transition peer-checked:translate-x-5"></span>
      </label>
    </div>
  </div>

  
  <div class="rounded-xl border border-border bg-card p-6">
    <div class="text-lg font-semibold mb-1 inline-flex items-center gap-2">
      <i data-lucide="shield-check" class="h-4 w-4"></i>
      Security
    </div>
    <p class="text-sm text-muted-foreground mb-6">Security and access settings</p>

    @php $s5 = $switch('require_2fa', (bool)$settings->require_2fa); @endphp
    <div class="flex items-center justify-between py-4 border-t border-border">
      <div>
        <div class="text-sm font-medium">Two-Factor Authentication</div>
        <div class="text-xs text-muted-foreground">Require 2FA for admin accounts</div>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="require_2fa" value="1" class="sr-only peer" {{ $s5['checkedAttr'] }}>
        <span class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary transition"></span>
        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-background transition peer-checked:translate-x-5"></span>
      </label>
    </div>
  </div>

</form>
@endsection
