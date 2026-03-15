@extends('layouts.dashboard')

@php
  $title = __('Workshops');
  $role  = 'Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>$applicationBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-muted-foreground">Manage your workshop postings</p>
    </div>

    <a href="{{ route('company.workshops.create') }}"
       class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Propose Workshop
    </a>
  </div>

  <div class="grid gap-4">
    @forelse($workshops as $w)
      <div class="rounded-xl border border-border bg-card">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="calendar-days" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $w['title'] }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $w['status_class'] }}">
                  {{ ucfirst($w['status']) }}
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1">
                  <i data-lucide="calendar-days" class="h-4 w-4"></i>
                  {{ $w['date'] }}
                </span>

                <span class="flex items-center gap-1">
                  <i data-lucide="clock" class="h-4 w-4"></i>
                  {{ $w['time'] }}
                </span>

                <span class="flex items-center gap-1">
                  <i data-lucide="map-pin" class="h-4 w-4"></i>
                  {{ $w['location'] }}
                </span>

                <span class="flex items-center gap-1">
                  <i data-lucide="users" class="h-4 w-4"></i>
                  {{ $w['registrations'] }} registrations
                </span>
              </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap justify-end">
              <a href="{{ route('company.workshops.manage', $w['id']) }}"
                 class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                 title="View Registrations">
                <i data-lucide="eye" class="h-4 w-4"></i>
              </a>

              <a href="{{ route('company.workshops.edit', $w['id']) }}"
                 class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                 title="Edit Workshop">
                <i data-lucide="file-pen-line" class="h-4 w-4"></i>
              </a>

              <form method="POST" action="{{ route('company.workshops.delete', $w['id']) }}"
                    onsubmit="return confirm('Delete this workshop?');">
                @csrf
                <button type="submit"
                        class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                        title="Delete Workshop">
                  <i data-lucide="trash-2" class="h-4 w-4"></i>
                </button>
              </form>
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
