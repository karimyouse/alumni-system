@extends('layouts.dashboard')

@php
  $title='Reports';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Alumni','href'=>'/college/alumni','icon'=>'users'],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days'],
    ['label'=>'Jobs','href'=>'/college/jobs','icon'=>'briefcase'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Reports</h1>
    <p class="text-sm text-muted-foreground">High-level statistics and system overview</p>
  </div>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Alumni</div>
      <div class="text-3xl font-bold mt-2">{{ $stats['alumni'] }}</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Companies</div>
      <div class="text-3xl font-bold mt-2">{{ $stats['companies'] }}</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Workshops</div>
      <div class="text-3xl font-bold mt-2">{{ $stats['workshops'] }}</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Jobs</div>
      <div class="text-3xl font-bold mt-2">{{ $stats['jobs'] }}</div>
    </div>
  </div>

  {{-- Content Summary --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Content Summary</div>
        <div class="text-sm text-muted-foreground">Published vs total</div>
      </div>

      <div class="p-6 space-y-4 text-sm">
        <div class="flex items-center justify-between">
          <span class="text-muted-foreground">Announcements</span>
          <span class="font-semibold">{{ $published['announcements'] }} / {{ $stats['announcements'] }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-muted-foreground">Success Stories</span>
          <span class="font-semibold">{{ $published['stories'] }} / {{ $stats['stories'] }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-muted-foreground">Scholarships</span>
          <span class="font-semibold">{{ $stats['scholarships'] }}</span>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Users Summary</div>
        <div class="text-sm text-muted-foreground">System roles</div>
      </div>

      <div class="p-6 space-y-4 text-sm">
        <div class="flex items-center justify-between">
          <span class="text-muted-foreground">College Users</span>
          <span class="font-semibold">{{ $stats['college_users'] }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-muted-foreground">Admins</span>
          <span class="font-semibold">{{ $stats['admins'] }}</span>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
