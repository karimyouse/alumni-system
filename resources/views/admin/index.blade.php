@extends('layouts.dashboard')

@php
  $title='Admin Dashboard';
  $role='Admin';

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'Company Approvals','href'=>'/admin/company-approvals','icon'=>'check-circle', 'badge'=>$pendingCompaniesCount ?? 0],
    ['label'=>'Users','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support','href'=>'/admin/support','icon'=>'life-buoy'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Admin Dashboard</h1>
      <p class="text-sm text-muted-foreground">Monitor system activity and approve companies</p>
    </div>

    <a href="/admin/company-approvals"
       class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2">
      <i data-lucide="check-circle" class="h-4 w-4"></i>
      Review Company Requests
    </a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Pending Companies</div>
      <div class="text-3xl font-bold mt-2">{{ $pendingCompaniesCount }}</div>
    </div>
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Total Companies</div>
      <div class="text-3xl font-bold mt-2">{{ $totalCompaniesCount }}</div>
    </div>
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Total Users</div>
      <div class="text-3xl font-bold mt-2">{{ $totalUsersCount }}</div>
    </div>
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Total Jobs</div>
      <div class="text-3xl font-bold mt-2">{{ $totalJobsCount }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">Pending Companies</div>
          <div class="text-sm text-muted-foreground">Latest registration requests</div>
        </div>
        <a href="/admin/company-approvals" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          View all <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-3">
        @forelse($pendingCompanies as $p)
          <div class="rounded-lg border border-border p-4 flex items-start justify-between gap-4">
            <div>
              <div class="font-semibold">{{ $p->company_name }}</div>
              <div class="text-xs text-muted-foreground">
                {{ $p->user?->email }} • {{ $p->industry ?? '—' }} • {{ $p->location ?? '—' }}
              </div>
            </div>
            <span class="text-xs rounded-full px-2 py-1 bg-orange-500/15 text-orange-400">Pending</span>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">No pending companies.</div>
        @endforelse
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div class="text-lg font-semibold inline-flex items-center gap-2">
          <i data-lucide="bell" class="h-4 w-4"></i>
          Notifications
        </div>
        <span class="text-xs rounded-full bg-secondary px-2 py-0.5">
          {{ $unreadNotificationsCount }}
        </span>
      </div>

      <div class="p-6 space-y-3">
        @forelse($recentNotifications as $n)
          <a href="{{ $n->action_url ?? '/admin/company-approvals' }}"
             class="block rounded-lg border border-border p-3 hover:bg-accent/30 transition">
            <div class="text-sm font-medium">{{ $n->title }}</div>
            <div class="text-xs text-muted-foreground mt-1">{{ $n->message }}</div>
            <div class="text-[11px] text-muted-foreground mt-2">{{ $n->created_at?->format('M d, Y • H:i') }}</div>
          </a>
        @empty
          <div class="text-sm text-muted-foreground">No notifications yet.</div>
        @endforelse
      </div>
    </div>

  </div>
</div>
@endsection
