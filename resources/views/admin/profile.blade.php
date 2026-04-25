@extends('layouts.dashboard')

@php
  $title = __('Admin Profile');
  $role='Admin';

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-check'],
    ['label'=>'Company Approvals','href'=>'/admin/company-approvals','icon'=>'check-circle','badge'=>$pendingCompanies ?? 0],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'life-buoy','badge'=>$openSupportTickets ?? 0],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
  ];
@endphp

@section('content')
<div class="max-w-5xl space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold leading-tight">Admin Profile</h1>
      <p class="text-sm text-muted-foreground">Manage your administrator identity and password.</p>
    </div>

    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex w-full items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
      Back
    </a>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive">
      <div class="font-semibold">Please fix the highlighted fields.</div>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
    <div class="space-y-6">
      <form method="POST" action="{{ route('admin.profile.update') }}"
            enctype="multipart/form-data"
            class="rounded-xl border border-border bg-card p-4 space-y-4 sm:p-6">
        @csrf

        <div>
          <div class="text-lg font-semibold">Account Information</div>
          <p class="mt-1 text-sm text-muted-foreground">Keep your admin identity clear for audit and support workflows.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="text-sm font-medium">Profile Photo</label>
            <input type="file"
                   name="profile_photo"
                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                   class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-primary/10 file:px-3 file:py-1 file:text-primary">
            <p class="mt-1 text-xs text-muted-foreground">Allowed: JPG, PNG, WEBP. Max size: 2MB.</p>
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-medium">Full Name</label>
            <input name="name"
                   value="{{ old('name', $user->name) }}"
                   class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                   required>
          </div>

          <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $user->email) }}"
                   class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">
            @error('email')
              <div class="mt-1 text-xs text-destructive">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <label class="text-sm font-medium">Role</label>
            <input value="{{ str_replace('_', ' ', ucfirst($user->role)) }}"
                   disabled
                   class="mt-1 w-full rounded-md border border-input bg-background/40 px-3 py-2 text-sm opacity-80">
          </div>
        </div>

        <button type="submit"
                class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 sm:w-auto">
          Save Profile
        </button>
      </form>

      @include('partials.account-password-card')
      @include('partials.session-security-card')
    </div>

    <div class="rounded-xl border border-border bg-card p-4 h-fit sm:p-6">
      <div class="flex items-center gap-3">
        @php($adminPhotoUrl = $user->profile_photo ? asset('storage/' . ltrim($user->profile_photo, '/')) : null)
        @if($adminPhotoUrl)
          <img src="{{ $adminPhotoUrl }}"
               alt="{{ $user->name }}"
               class="h-12 w-12 flex-shrink-0 rounded-full border border-border object-cover">
        @else
          <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
            <i data-lucide="shield-check" class="h-6 w-6"></i>
          </div>
        @endif
        <div class="min-w-0">
          <div class="font-semibold break-words">{{ $user->name }}</div>
          <div class="text-sm text-muted-foreground break-all">{{ $user->email }}</div>
        </div>
      </div>

      <div class="mt-6 grid gap-3 text-sm">
        <div class="rounded-md bg-muted/40 px-3 py-2">
          <div class="text-xs text-muted-foreground">Total Users</div>
          <div class="font-semibold">{{ number_format($totalUsers ?? 0) }}</div>
        </div>
        <div class="rounded-md bg-muted/40 px-3 py-2">
          <div class="text-xs text-muted-foreground">Pending Companies</div>
          <div class="font-semibold">{{ number_format($pendingCompanies ?? 0) }}</div>
        </div>
        <div class="rounded-md bg-muted/40 px-3 py-2">
          <div class="text-xs text-muted-foreground">Open Support Tickets</div>
          <div class="font-semibold">{{ number_format($openSupportTickets ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
