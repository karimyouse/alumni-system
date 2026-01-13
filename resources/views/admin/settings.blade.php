@extends('layouts.dashboard')

@php
  $title = 'Settings';
  $role  = 'Admin';

  $nav = [
    ['label'=>'Overview', 'href'=>'/admin', 'icon'=>'layout-dashboard'],
    ['label'=>'Users',    'href'=>'/admin/users', 'icon'=>'users'],
    ['label'=>'Content',  'href'=>'/admin/content', 'icon'=>'file-text'],
    ['label'=>'Reports',  'href'=>'/admin/reports', 'icon'=>'bar-chart-3'],
    ['label'=>'Settings', 'href'=>'/admin/settings', 'icon'=>'settings'],
    ['label'=>'Support',  'href'=>'/admin/support', 'icon'=>'help-circle'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Settings</h1>
    <p class="text-muted-foreground">Configure system settings</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Appearance</div>
        <div class="text-sm text-muted-foreground">Customize the look and feel</div>
      </div>

      <div class="p-6 space-y-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">Institution Name</label>
          <div class="text-xs text-muted-foreground">Displayed in the header</div>
          <input class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm" value="Palestine Technical College" />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Primary Color</label>
          <div class="text-xs text-muted-foreground">Main theme color</div>
          <input class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm" value="#0ea5e9" />
        </div>
      </div>
    </div>


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Email Notifications</div>
        <div class="text-sm text-muted-foreground">Configure email settings</div>
      </div>

      <div class="p-6 space-y-4">
        @foreach([
          ['New User Notifications','Email admins when new users register', true],
          ['Content Approval Alerts','Email when content needs approval', true],
          ['Weekly Reports','Send weekly summary emails', false],
        ] as [$t,$d,$on])
          <div class="flex items-start justify-between gap-4">
            <div>
              <div class="font-medium">{{ $t }}</div>
              <div class="text-sm text-muted-foreground">{{ $d }}</div>
            </div>

            <label class="inline-flex items-center cursor-pointer">
              <input type="checkbox" class="sr-only peer" {{ $on ? 'checked' : '' }}>
              <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary relative transition">
                <div class="w-5 h-5 bg-background rounded-full absolute top-0.5 left-0.5 peer-checked:left-5 transition"></div>
              </div>
            </label>
          </div>
        @endforeach
      </div>
    </div>


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Data Management</div>
        <div class="text-sm text-muted-foreground">Backup and restore options</div>
      </div>

      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <div class="font-medium">Last Backup</div>
            <div class="text-sm text-muted-foreground">Dec 22, 2025 at 3:00 AM</div>
          </div>
          <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
            Backup Now
          </button>
        </div>

        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-medium">Auto Backup</div>
            <div class="text-sm text-muted-foreground">Daily automatic backups</div>
          </div>
          <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" checked>
            <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary relative transition">
              <div class="w-5 h-5 bg-background rounded-full absolute top-0.5 left-0.5 peer-checked:left-5 transition"></div>
            </div>
          </label>
        </div>
      </div>
    </div>

    
    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Security</div>
        <div class="text-sm text-muted-foreground">Security and access settings</div>
      </div>

      <div class="p-6 space-y-4">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-medium">Two-Factor Authentication</div>
            <div class="text-sm text-muted-foreground">Require 2FA for admin accounts</div>
          </div>
          <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer">
            <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary relative transition">
              <div class="w-5 h-5 bg-background rounded-full absolute top-0.5 left-0.5 peer-checked:left-5 transition"></div>
            </div>
          </label>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Session Timeout (minutes)</label>
          <div class="text-sm text-muted-foreground">Auto-logout inactive users</div>
          <input class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm" value="120" />
        </div>

        <button class="w-full rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
          Save Changes
        </button>
      </div>
    </div>

  </div>
</div>
@endsection
