@extends('layouts.dashboard')

@php
  $title = __('Workshops');
  $role  = 'Company';

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
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-sm text-muted-foreground sm:text-base">Participate and manage workshop collaborations</p>
    </div>

    <a href="{{ route('company.workshops.create') }}"
       class="inline-flex w-full items-center justify-center rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Propose Workshop
    </a>
  </div>

  <div class="grid gap-4">
    @forelse($workshops as $w)
      <div class="rounded-xl border border-border bg-card">
        <div class="p-4 sm:p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="calendar-days" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-semibold leading-snug break-words">{{ $w['title'] }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $w['status_class'] }}">
                  {{ ucfirst($w['status']) }}
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1"><i data-lucide="calendar-days" class="h-4 w-4"></i>{{ $w['date'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="clock" class="h-4 w-4"></i>{{ $w['time'] }}</span>
                <span class="flex items-center gap-1 break-words"><i data-lucide="map-pin" class="h-4 w-4"></i>{{ $w['location'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="users" class="h-4 w-4"></i>{{ $w['registrations'] }} registrations</span>
              </div>
            </div>

            <div class="w-full md:w-auto">
              <a href="{{ route('company.workshops.manage', $w['id']) }}"
                 class="inline-flex w-full items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 md:w-auto">
                Manage
              </a>
            </div>

          </div>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No workshops found.
      </div>
    @endforelse
  </div>
</div>
@endsection
