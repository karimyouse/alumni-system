@extends('layouts.dashboard')

@php
  $title = __('Browse Alumni');
  $role='Company';

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

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">Browse Alumni</h1>
      <p class="text-sm text-muted-foreground">Browse and connect with qualified graduates</p>
    </div>
  </div>

  <form method="GET" action="{{ route('company.alumni') }}" class="rounded-xl border border-border bg-card p-5">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <div>
        <label class="text-sm font-medium">Search</label>
        <input name="search"
               value="{{ $search }}"
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Name / Email / Academic ID">
      </div>

      <div>
        <label class="text-sm font-medium">Major</label>
        <select name="major" class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
          <option value="">All</option>
          @foreach($majors as $item)
            <option value="{{ $item }}" {{ $major === $item ? 'selected' : '' }}>{{ $item }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-sm font-medium">Location</label>
        <select name="location" class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
          <option value="">All</option>
          @foreach($locations as $item)
            <option value="{{ $item }}" {{ $location === $item ? 'selected' : '' }}>{{ $item }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-sm font-medium">Skill</label>
        <input name="skill"
               value="{{ $skill }}"
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="e.g. Laravel">
      </div>
    </div>

    <div class="flex items-center gap-3 mt-4">
      <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Apply Filters
      </button>

      <a href="{{ route('company.alumni') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Reset
      </a>
    </div>
  </form>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
    @forelse($alumni as $alumnus)
      <div class="rounded-xl border border-border bg-card p-5">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-base font-semibold flex-shrink-0">
            {{ $alumnus->display_initials }}
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3 flex-wrap">
              <h3 class="font-semibold text-lg leading-tight">{{ $alumnus->name }}</h3>

              <span class="text-[11px] rounded-full px-3 py-1 {{ $alumnus->display_status_class }}">
                {{ $alumnus->display_status_label }}
              </span>
            </div>

            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-2 text-sm text-muted-foreground">
              <span class="inline-flex items-center gap-1.5">
                <i data-lucide="graduation-cap" class="h-3.5 w-3.5"></i>
                {{ $alumnus->display_major_year }}
              </span>

              <span class="inline-flex items-center gap-1.5">
                <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                {{ $alumnus->display_location }}
              </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
              @forelse($alumnus->display_skills as $skillItem)
                <span class="text-[11px] rounded-full bg-secondary px-3 py-1 text-secondary-foreground">
                  {{ $skillItem }}
                </span>
              @empty
                <span class="text-xs text-muted-foreground">No skills listed</span>
              @endforelse
            </div>

            <div class="mt-4 flex items-center gap-3 flex-wrap">
              <a href="{{ route('company.alumni.show', $alumnus) }}"
                 class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
                View Profile
              </a>

              <a href="mailto:{{ $alumnus->email }}"
                 class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
                <i data-lucide="mail" class="h-4 w-4"></i>
                Contact
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground xl:col-span-2">
        No alumni found for the selected filters.
      </div>
    @endforelse
  </div>

  <div>
    {{ $alumni->links() }}
  </div>

</div>
@endsection
