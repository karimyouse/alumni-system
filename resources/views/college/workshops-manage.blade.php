@extends('layouts.dashboard')

@php
  $title='Manage Workshop';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Manage Alumni','href'=>'/college/alumni','icon'=>'users'],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days'],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">{{ $workshop->title }}</h1>
      <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
        <span class="inline-flex items-center gap-2">
          <i data-lucide="calendar" class="h-4 w-4"></i>
          {{ $workshop->date }}
        </span>
        <span class="inline-flex items-center gap-2">
          <i data-lucide="clock" class="h-4 w-4"></i>
          {{ $workshop->time }}
        </span>
        <span class="inline-flex items-center gap-2">
          <i data-lucide="map-pin" class="h-4 w-4"></i>
          {{ $workshop->location }}
        </span>
        <span class="inline-flex items-center gap-2">
          <i data-lucide="users" class="h-4 w-4"></i>
          {{ $workshop->display_spots_label }}
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('college.workshops.edit', $workshop) }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition">
        Edit
      </a>

      <a href="{{ route('college.workshops') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition">
        Back
      </a>
    </div>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="p-5 border-b border-border">
      <div class="text-lg font-semibold">Registrations</div>
      <div class="text-sm text-muted-foreground">{{ $registrations->count() }} registered alumni</div>
    </div>

    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Alumnus</th>
            <th class="text-left p-4 font-medium">Academic ID</th>
            <th class="text-left p-4 font-medium">Email</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registrations as $r)
            <tr class="border-b last:border-0">
              <td class="p-4 font-medium">{{ $r->alumni?->name ?? 'Alumni' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $r->alumni?->academic_id ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $r->alumni?->email ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="3">No registrations yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
