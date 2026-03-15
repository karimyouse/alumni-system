@extends('layouts.dashboard')

@php
  $title = __('Admin Dashboard');

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $userName = auth()->user()->name ?? 'Admin';
@endphp

@section('content')
<div class="space-y-6">

  <div class="rounded-xl border border-orange-500/20 bg-gradient-to-r from-orange-500/10 via-orange-500/5 to-transparent">
    <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center">
          <i data-lucide="shield" class="h-6 w-6 text-orange-400"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold mb-1">Welcome, {{ $userName }}!</h2>
          <p class="text-muted-foreground">System overview and administration panel.</p>
        </div>
      </div>

      <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-400 px-3 py-1 text-xs font-medium">
        <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
        Dashboard connected to live data
      </span>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Users</div>
        <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="users" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($totalUsers) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">
        New this month: {{ number_format($newUsersThisMonth) }}
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Pending Approvals</div>
        <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="building-2" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($pendingApprovals) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">Company registrations awaiting review</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Active Job Posts</div>
        <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="briefcase" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($activeJobPosts) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">
        New jobs this month: {{ number_format($newJobsThisMonth) }}
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Monthly Logins</div>
        <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="trending-up" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($monthlyLogins) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">Users who logged in this month</div>
    </div>

  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">Pending Company Approvals</div>
          <div class="text-sm text-muted-foreground">Review and approve company registrations</div>
        </div>

        <a href="{{ route('admin.companyApprovals') }}" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          View All
          <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @forelse($pendingCompanies as $p)
          @php
            $companyName = $p->company_name ?? ($p->name ?? 'Company');
            $industry = $p->industry ?? '—';
            $email = $p->email ?? '—';
            $applied = $p->created_at ? \Carbon\Carbon::parse($p->created_at)->diffForHumans() : 'Recently';
          @endphp

          <div class="flex items-center justify-between gap-4 rounded-xl border border-border bg-accent/20 p-4">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-lg bg-purple-500/15 text-purple-400 flex items-center justify-center">
                <i data-lucide="building-2" class="h-5 w-5"></i>
              </div>

              <div class="min-w-0">
                <div class="font-semibold truncate">{{ $companyName }}</div>
                <div class="text-xs text-muted-foreground truncate">{{ $industry }} • {{ $email }}</div>
                <div class="text-xs text-muted-foreground inline-flex items-center gap-1 mt-1">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  Applied {{ $applied }}
                </div>
              </div>
            </div>

            <a href="{{ route('admin.companyApprovals', ['status'=>'pending']) }}"
               class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              Review
            </a>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">No pending approvals right now.</div>
        @endforelse
      </div>
    </div>

    <div class="space-y-6">

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="database" class="h-4 w-4"></i>
            System Snapshot
          </div>
        </div>

        <div class="p-6 space-y-4">
          @foreach($systemSnapshot as $item)
            <div class="flex items-center justify-between gap-3 p-3 rounded-lg bg-accent/30">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                  <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                </div>
                <span class="text-sm">{{ $item['label'] }}</span>
              </div>
              <span class="text-sm font-semibold">{{ number_format($item['value']) }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="activity" class="h-4 w-4"></i>
            Recent Activity
          </div>
        </div>

        <div class="p-6 space-y-4">
          @forelse($recentActivity as $a)
            <div class="flex items-start gap-3">
              <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <i data-lucide="{{ $a['icon'] ?? 'activity' }}" class="h-4 w-4"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm font-medium">{{ $a['title'] ?? '' }}</div>
                <div class="text-xs text-muted-foreground">{{ $a['time'] ?? '' }}</div>
              </div>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">No recent activity.</div>
          @endforelse
        </div>
      </div>

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="users" class="h-4 w-4"></i>
            Users by Role
          </div>
        </div>

        <div class="p-6 space-y-3">
          @foreach($usersByRole as $r)
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary"></div>
                <span class="text-sm">{{ $r['role'] }}</span>
              </div>
              <span class="text-sm font-medium">{{ number_format($r['count']) }}</span>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  <div class="rounded-xl border border-border bg-card">
    <div class="p-6 border-b border-border">
      <div class="text-lg font-semibold">Quick Actions</div>
      <div class="text-sm text-muted-foreground">Common administrative tasks</div>
    </div>

    <div class="p-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.users') }}" class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2">
          <i data-lucide="users" class="h-5 w-5"></i>
          <span class="text-sm">Manage Users</span>
        </a>

        <a href="{{ route('admin.companyApprovals') }}" class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2">
          <i data-lucide="building-2" class="h-5 w-5"></i>
          <span class="text-sm">Review Approvals</span>
        </a>

        <a href="{{ route('admin.reports') }}" class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2">
          <i data-lucide="bar-chart-3" class="h-5 w-5"></i>
          <span class="text-sm">View Analytics</span>
        </a>

        <a href="{{ route('admin.settings') }}" class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2">
          <i data-lucide="settings" class="h-5 w-5"></i>
          <span class="text-sm">System Settings</span>
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
