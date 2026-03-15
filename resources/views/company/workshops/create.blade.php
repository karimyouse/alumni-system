@extends('layouts.dashboard')

@php
  $title = __('Propose Workshop');
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
<div class="max-w-2xl space-y-6">

  <div>
    <h1 class="text-2xl font-bold">{{ ($isEdit ?? false) ? 'Edit Workshop' : 'Propose Workshop' }}</h1>
    <p class="text-sm text-muted-foreground">
      {{ ($isEdit ?? false) ? 'Update your workshop and send it again for college review' : 'Submit a new workshop proposal' }}
    </p>
  </div>

  <form method="POST"
        action="{{ ($isEdit ?? false) ? route('company.workshops.update', $workshop) : route('company.workshops.store') }}"
        class="rounded-xl border border-border bg-card p-6 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-medium">Title</label>
      <input name="title" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             placeholder="Tech Career Day 2026"
             value="{{ old('title', $workshop->title ?? '') }}">
      @error('title') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="grid md:grid-cols-2 gap-3">
      <div>
        <label class="text-sm font-medium">Date</label>
        <input name="date" required
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Feb 15, 2026"
               value="{{ old('date', $workshop->date ?? '') }}">
        @error('date') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium">Time</label>
        <input name="time" required
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="10:00 AM - 2:00 PM"
               value="{{ old('time', $workshop->time ?? '') }}">
        @error('time') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Location</label>
      <input name="location" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             placeholder="Main Campus"
             value="{{ old('location', $workshop->location ?? '') }}">
      @error('location') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
      <label class="text-sm font-medium">Capacity (optional)</label>
      <input name="capacity" type="number" min="1"
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             placeholder="e.g. 50"
             value="{{ old('capacity', $workshop->capacity ?? '') }}">
    </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        {{ ($isEdit ?? false) ? 'Save Changes' : 'Submit' }}
      </button>

      <a href="{{ route('company.workshops') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Cancel
      </a>
    </div>
  </form>

</div>
@endsection
