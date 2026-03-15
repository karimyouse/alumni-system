@extends('layouts.dashboard')

@php
 $title = __('User Management');


  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $tab = $tab ?? request('tab', 'alumni');
  $q   = $q ?? request('q', '');

  $counts = $counts ?? ['alumni'=>0,'college'=>0,'companies'=>0];
@endphp

@section('content')
<style>
  summary::-webkit-details-marker { display:none; }
</style>

<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">User Management</h1>
      <p class="text-sm text-muted-foreground">Manage all user accounts</p>
    </div>

    <form method="GET" action="{{ route('admin.users') }}" class="w-full max-w-xs">
      <input type="hidden" name="tab" value="{{ $tab }}">
      <div class="relative">
        <i data-lucide="search" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"></i>
        <input name="q" value="{{ $q }}" placeholder="Search users..."
               class="w-full rounded-md border border-input bg-background/60 pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">
      </div>
    </form>
  </div>


  @php
    $tabs = [
      ['key'=>'alumni','label'=>'Alumni','count'=>$counts['alumni'] ?? 0],
      ['key'=>'college','label'=>'College','count'=>$counts['college'] ?? 0],
      ['key'=>'companies','label'=>'Companies','count'=>$counts['companies'] ?? 0],
    ];
  @endphp

  <div class="flex items-center gap-2">
    @foreach($tabs as $t)
      <a href="{{ route('admin.users', ['tab'=>$t['key'], 'q'=>$q]) }}"
         class="rounded-md px-3 py-1.5 text-sm border border-border transition
         {{ $tab === $t['key'] ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
        {{ $t['label'] }} ({{ $t['count'] }})
      </a>
    @endforeach
  </div>


  <div class="rounded-xl border border-border bg-card">
    <div class="grid grid-cols-12 gap-4 px-6 py-3 border-b border-border text-xs text-muted-foreground">
      <div class="col-span-5">User</div>
      <div class="col-span-2">Status</div>
      <div class="col-span-3">Last Login</div>
      <div class="col-span-2 text-right">Actions</div>
    </div>

    <div class="divide-y divide-border">
      @forelse($users as $u)
        @php
          $name = $u->name ?? 'User';
          $email = $u->email ?? '—';
          $roleLabel = ucfirst(str_replace('_',' ', $u->role ?? 'user'));

          $initials = collect(explode(' ', $name))->map(fn($n)=>mb_substr($n,0,1))->join('');
          $initials = $initials ?: 'U';

          $isSuspended = (bool)($u->is_suspended ?? false);

          $statusText = $isSuspended ? 'suspended' : 'active';
          $statusClass = $isSuspended ? 'bg-red-500/15 text-red-400' : 'bg-blue-500/15 text-blue-400';

          $lastLogin = $u->last_login_at ? $u->last_login_at->format('M d, Y') : '—';
        @endphp

        <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center">
          <div class="col-span-5 flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
              {{ $initials }}
            </div>
            <div class="min-w-0">
              <div class="text-sm font-medium truncate">{{ $name }}</div>
              <div class="text-xs text-muted-foreground truncate">{{ $email }}</div>
              <div class="text-xs text-muted-foreground truncate">Role: {{ $roleLabel }}</div>
            </div>
          </div>

          <div class="col-span-2">
            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $statusClass }}">
              {{ $statusText }}
            </span>
          </div>

          <div class="col-span-3 text-sm text-muted-foreground">
            {{ $lastLogin }}
          </div>

          <div class="col-span-2 flex items-center justify-end gap-2">

            <a href="{{ route('admin.users.show', $u) }}"
               class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50">
              <i data-lucide="eye" class="h-4 w-4 text-muted-foreground"></i>
            </a>


            <form method="POST" action="{{ route('admin.users.suspend', $u) }}">
              @csrf
              <button type="submit"
                      class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      title="{{ $isSuspended ? 'Activate' : 'Suspend' }}">
                <i data-lucide="ban" class="h-4 w-4 {{ $isSuspended ? 'text-green-400' : 'text-red-400' }}"></i>
              </button>
            </form>


            <details class="relative">
              <summary class="h-9 w-9 cursor-pointer inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50">
                <i data-lucide="more-horizontal" class="h-4 w-4 text-muted-foreground"></i>
              </summary>

              <div class="absolute right-0 mt-2 w-56 rounded-xl border border-border bg-card shadow-xl z-50 overflow-hidden">
                <div class="px-3 py-2 text-xs text-muted-foreground border-b border-border">Actions</div>

                <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-accent/40">
                  <i data-lucide="user" class="h-4 w-4"></i>
                  View profile
                </a>

                <form method="POST" action="{{ route('admin.users.role', $u) }}" class="px-3 py-2 border-t border-border">
                  @csrf
                  <div class="text-xs text-muted-foreground mb-1">Change role</div>
                  <div class="flex items-center gap-2">
                    <select name="role" class="w-full rounded-md border border-input bg-background/60 px-2 py-1 text-sm">
                      @foreach(['alumni','college','company','admin','super_admin'] as $r)
                        <option value="{{ $r }}" {{ ($u->role === $r) ? 'selected' : '' }}>
                          {{ ucfirst(str_replace('_',' ', $r)) }}
                        </option>
                      @endforeach
                    </select>
                    <button class="rounded-md bg-primary px-3 py-1.5 text-xs text-primary-foreground hover:opacity-90">
                      Save
                    </button>
                  </div>
                </form>

                <form method="POST" action="{{ route('admin.users.suspend', $u) }}" class="border-t border-border">
                  @csrf
                  <button class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-accent/40">
                    <i data-lucide="ban" class="h-4 w-4 {{ $isSuspended ? 'text-green-400' : 'text-red-400' }}"></i>
                    {{ $isSuspended ? 'Activate user' : 'Suspend user' }}
                  </button>
                </form>
              </div>
            </details>
          </div>
        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No users found.</div>
      @endforelse
    </div>
  </div>

  @if(method_exists($users, 'links'))
    <div class="pt-2">
      {{ $users->links() }}
    </div>
  @endif

</div>
@endsection
