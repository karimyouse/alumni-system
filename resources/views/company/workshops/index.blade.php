@extends('layouts.dashboard')

@php
  $title='Workshops';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-sm text-muted-foreground">Participate and manage workshop collaborations</p>
    </div>

    <a href="{{ route('company.workshops.create') }}"
       class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Propose Workshop
    </a>
  </div>

  <div class="space-y-4">
    @forelse($workshops as $w)
      <div class="rounded-xl border border-border bg-card p-5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
          <div class="w-12 h-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <i data-lucide="calendar-days" class="h-6 w-6"></i>
          </div>

          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <h3 class="text-lg font-semibold truncate">{{ $w['title'] }}</h3>
              <span class="text-xs rounded-full px-2 py-1 {{ $w['state_class'] }}">{{ $w['state'] }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground mt-2">
              <span class="inline-flex items-center gap-2">
                <i data-lucide="calendar" class="h-4 w-4"></i> {{ $w['date'] }}
              </span>
              <span class="inline-flex items-center gap-2">
                <i data-lucide="clock" class="h-4 w-4"></i> {{ $w['time'] }}
              </span>
              <span class="inline-flex items-center gap-2">
                <i data-lucide="map-pin" class="h-4 w-4"></i> {{ $w['location'] }}
              </span>
              <span class="inline-flex items-center gap-2">
                <i data-lucide="users" class="h-4 w-4"></i> {{ $w['registrations'] }} registrations
              </span>
            </div>
          </div>
        </div>

        <a href="{{ route('company.workshops.manage', $w['id']) }}"
           class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          Manage
        </a>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No workshops yet.
      </div>
    @endforelse
  </div>

</div>
@endsection
