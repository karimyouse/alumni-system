@extends('layouts.dashboard')

@php
  $title = __('User Profile');


  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $name = $user->name ?? 'User';
  $email = $user->email ?? '—';
  $role = $user->role ?? '—';
  $academicId = $user->academic_id ?? '—';

  $lastLogin = $user->last_login_at ? $user->last_login_at->format('M d, Y • h:i A') : '—';
  $statusLabel = $user->is_suspended ? 'suspended' : 'active';
  $statusClass = $user->is_suspended ? 'bg-red-500/15 text-red-400' : 'bg-blue-500/15 text-blue-400';

  $initials = collect(explode(' ', $name))->map(fn($n)=>mb_substr($n,0,1))->join('');
  $initials = $initials ?: 'U';
@endphp

@section('content')
<div class="space-y-6 max-w-3xl">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold">User Profile</h1>
      <p class="text-sm text-muted-foreground">View and manage user details</p>
    </div>

    <a href="{{ route('admin.users') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
    <div class="flex items-start gap-4">
      <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold flex-shrink-0">
        {{ $initials }}
      </div>

      <div class="min-w-0 flex-1">
        <div class="text-lg font-semibold break-words">{{ $name }}</div>
        <div class="text-sm text-muted-foreground break-words">{{ $email }}</div>
      </div>

      <div class="ml-auto flex-shrink-0">
        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs {{ $statusClass }}">
          {{ $statusLabel }}
        </span>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="rounded-lg border border-border p-4">
        <div class="text-xs text-muted-foreground">Role</div>
        <div class="text-sm font-medium mt-1">{{ $role }}</div>
      </div>

      <div class="rounded-lg border border-border p-4">
        <div class="text-xs text-muted-foreground">Academic ID</div>
        <div class="text-sm font-medium mt-1">{{ $academicId }}</div>
      </div>

      <div class="rounded-lg border border-border p-4 md:col-span-2">
        <div class="text-xs text-muted-foreground">Last Login</div>
        <div class="text-sm font-medium mt-1">{{ $lastLogin }}</div>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-2 sm:flex sm:items-center">

      <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
        @csrf
        <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
          <i data-lucide="ban" class="h-4 w-4 {{ $user->is_suspended ? 'text-green-400' : 'text-red-400' }}"></i>
          {{ $user->is_suspended ? 'Activate User' : 'Suspend User' }}
        </button>
      </form>

      <form method="POST" action="{{ route('admin.users.role', $user) }}" class="grid grid-cols-1 gap-2 sm:flex sm:items-center">
        @csrf
        <select name="role" class="h-9 w-full rounded-md border border-input bg-background/60 px-2 text-sm sm:w-auto">
          @foreach(['alumni','company','college','admin','super_admin'] as $r)
            <option value="{{ $r }}" {{ $role === $r ? 'selected' : '' }}>{{ $r }}</option>
          @endforeach
        </select>
        <button type="submit"
                class="h-9 rounded-md bg-primary px-3 text-sm text-primary-foreground hover:opacity-90">
          Save Role
        </button>
      </form>

    </div>
  </div>

</div>
@endsection
