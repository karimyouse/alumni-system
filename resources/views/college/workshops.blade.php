@extends('layouts.dashboard')

@php
  $title = 'Workshops';
  $role  = 'College';

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

  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-sm text-muted-foreground">Create and manage public workshops for alumni</p>
    </div>

    <a href="{{ url('/college/workshops/create') }}"
       class="relative z-[9999] pointer-events-auto rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 transition inline-flex items-center gap-2">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Add Workshop
    </a>
  </div>

  <div class="space-y-4">
    @forelse($workshops as $w)
      <div class="rounded-xl border border-border bg-card p-6 flex items-center justify-between">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            <i data-lucide="calendar-days" class="h-5 w-5"></i>
          </div>

          <div>
            <h3 class="font-semibold text-lg">{{ $w->title }}</h3>

            <div class="mt-2 flex flex-wrap gap-3 text-xs text-muted-foreground">
              <span class="inline-flex items-center gap-1">
                <i data-lucide="calendar" class="h-3 w-3"></i>
                {{ $w->date }}
              </span>

              <span class="inline-flex items-center gap-1">
                <i data-lucide="clock" class="h-3 w-3"></i>
                {{ $w->time }}
              </span>

              <span class="inline-flex items-center gap-1">
                <i data-lucide="map-pin" class="h-3 w-3"></i>
                {{ $w->location }}
              </span>

              @if(!is_null($w->capacity))
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="users" class="h-3 w-3"></i>
                  Capacity: {{ $w->capacity }}
                </span>
              @endif
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          {{-- لاحقًا: زر Manage / Edit / Delete --}}
          <span class="text-xs rounded-full bg-secondary px-2 py-1 text-secondary-foreground">
            Public
          </span>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No workshops yet. Click <b>Add Workshop</b> to create one.
      </div>
    @endforelse
  </div>

</div>
@endsection
