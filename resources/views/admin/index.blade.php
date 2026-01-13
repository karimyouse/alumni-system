@extends('layouts.dashboard')

@php
  $title = 'Admin Dashboard';
  $role  = 'Admin';

  $nav = [
    ['label'=>'Overview', 'href'=>'/admin',        'icon'=>'layout-dashboard'],
    ['label'=>'Users',    'href'=>'/admin/users',  'icon'=>'users'],
    ['label'=>'Content',  'href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports',  'href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'Settings', 'href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support',  'href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $pendingCompanies = [
    ['id'=>'1','name'=>'TechCorp Solutions','industry'=>'Technology','applied'=>'2 hours ago','email'=>'contact@techcorp.com'],
    ['id'=>'2','name'=>'StartupX','industry'=>'Software','applied'=>'1 day ago','email'=>'hr@startupx.com'],
    ['id'=>'3','name'=>'DesignHub Agency','industry'=>'Design','applied'=>'2 days ago','email'=>'info@designhub.com'],
  ];

  $recentActivity = [
    ['id'=>'1','action'=>'New alumni registered','user'=>'Ahmed Hassan','time'=>'10 min ago','icon'=>'user-plus'],
    ['id'=>'2','action'=>'Job post approved','user'=>'College Admin','time'=>'1 hour ago','icon'=>'briefcase'],
    ['id'=>'3','action'=>'Company approved','user'=>'System','time'=>'3 hours ago','icon'=>'building-2'],
    ['id'=>'4','action'=>'Workshop created','user'=>'College Admin','time'=>'5 hours ago','icon'=>'graduation-cap'],
  ];

  $systemHealth = [
    ['name'=>'Server Uptime','value'=>99.9,'status'=>'Healthy'],
    ['name'=>'Database','value'=>98.5,'status'=>'Healthy'],
    ['name'=>'API Response','value'=>95.2,'status'=>'Good'],
  ];

  $usersByRole = [
    ['role'=>'Alumni','count'=>2547,'percentage'=>85],
    ['role'=>'College Staff','count'=>24,'percentage'=>1],
    ['role'=>'Companies','count'=>48,'percentage'=>2],
    ['role'=>'Admins','count'=>5,'percentage'=>0.2],
  ];
@endphp

@section('content')
<div class="space-y-6">


  <div class="rounded-xl border border-border bg-card">
    <div class="p-6">
      <div class="text-2xl font-bold">Welcome, {{ auth()->user()->name ?? 'Admin' }}!</div>
      <div class="text-sm text-muted-foreground mt-1">System overview and management tools</div>
    </div>
  </div>


  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Users</div>
        <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">2,624</div>
      <div class="text-xs text-muted-foreground mt-1">Across all roles</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Active Companies</div>
        <i data-lucide="building-2" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">48</div>
      <div class="text-xs text-muted-foreground mt-1">Verified partners</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Pending Approvals</div>
        <i data-lucide="clock" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ count($pendingCompanies) }}</div>
      <div class="text-xs text-muted-foreground mt-1">Company requests</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">System Health</div>
        <i data-lucide="activity" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">99%</div>
      <div class="text-xs text-muted-foreground mt-1">Overall status</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">Pending Company Approvals</div>
          <div class="text-sm text-muted-foreground">Review new company registrations</div>
        </div>
        <a href="/admin/users" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          Manage users <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @foreach($pendingCompanies as $c)
          <div class="flex items-center justify-between gap-4 p-4 rounded-lg border border-border">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <i data-lucide="building-2" class="h-5 w-5 text-purple-400"></i>
              </div>
              <div class="min-w-0">
                <div class="font-medium truncate">{{ $c['name'] }}</div>
                <div class="text-sm text-muted-foreground truncate">{{ $c['industry'] }} • {{ $c['email'] }}</div>
                <div class="text-xs text-muted-foreground mt-1">Applied {{ $c['applied'] }}</div>
              </div>
            </div>

            <div class="flex gap-2 flex-shrink-0">
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Reject</button>
              <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">Approve</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>


    <div class="space-y-6">

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold">System Health</div>
        </div>
        <div class="p-6 space-y-4">
          @foreach($systemHealth as $h)
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm">{{ $h['name'] }}</span>
                <span class="text-sm font-medium">{{ $h['value'] }}%</span>
              </div>
              <div class="h-2 rounded-full bg-muted overflow-hidden">
                <div class="h-2 rounded-full bg-primary/80" style="width: {{ $h['value'] }}%"></div>
              </div>
              <div class="text-xs text-muted-foreground mt-1">{{ $h['status'] }}</div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold">Users by Role</div>
        </div>
        <div class="p-6 space-y-4">
          @foreach($usersByRole as $u)
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm">{{ $u['role'] }}</span>
                <span class="text-sm font-medium">{{ $u['count'] }}</span>
              </div>
              <div class="h-2 rounded-full bg-muted overflow-hidden">
                <div class="h-2 rounded-full bg-primary/80" style="width: {{ $u['percentage'] }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  
  <div class="rounded-xl border border-border bg-card">
    <div class="p-6 border-b border-border">
      <div class="text-lg font-semibold">Recent Activity</div>
      <div class="text-sm text-muted-foreground">Latest actions across the system</div>
    </div>

    <div class="p-6 grid md:grid-cols-2 gap-4">
      @foreach($recentActivity as $a)
        <div class="flex items-start gap-3 p-4 rounded-lg border border-border">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
            <i data-lucide="{{ $a['icon'] }}" class="h-5 w-5 text-primary"></i>
          </div>
          <div class="flex-1">
            <div class="font-medium">{{ $a['action'] }}</div>
            <div class="text-sm text-muted-foreground">{{ $a['user'] }} • {{ $a['time'] }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
