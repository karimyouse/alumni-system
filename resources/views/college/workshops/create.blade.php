@extends('layouts.dashboard')

@php
  $title = $isEdit ? __('Edit Workshop') : __('Add Workshop');
  $role  = 'College';

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
<div class="space-y-6 max-w-4xl">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">{{ $isEdit ? 'Edit Workshop' : 'Add Workshop' }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ $isEdit ? 'Update workshop details and settings' : 'Create a new workshop for alumni' }}
      </p>
    </div>

    <a href="{{ route('college.workshops') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-6">
    <form method="POST"
          action="{{ $isEdit ? route('college.workshops.update', $workshop) : route('college.workshops.store') }}"
          class="space-y-5">
      @csrf

      <div>
        <label class="text-sm font-medium">Title <span class="text-destructive">*</span></label>
        <input name="title"
               value="{{ old('title', $workshop->title ?? '') }}"
               required
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
               placeholder="Career Development Workshop">
        @error('title') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">Date <span class="text-destructive">*</span></label>
          <input name="date"
                 value="{{ old('date', $workshop->date ?? '') }}"
                 required
                 class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="Jan 28, 2026">
          @error('date') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-medium">Time <span class="text-destructive">*</span></label>
          <input name="time"
                 value="{{ old('time', $workshop->time ?? '') }}"
                 required
                 class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="2:00 PM - 4:00 PM">
          @error('time') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">Location <span class="text-destructive">*</span></label>
          <input name="location"
                 value="{{ old('location', $workshop->location ?? '') }}"
                 required
                 class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="Main Campus / Online / Hall A">
          @error('location') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-medium">Capacity (optional)</label>
          <input name="capacity"
                 value="{{ old('capacity', $workshop->capacity ?? ($workshop->max_spots ?? '')) }}"
                 class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="50">
          <div class="text-xs text-muted-foreground mt-1">Leave empty for unlimited seats.</div>
          @error('capacity') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="pt-2">
        <button type="submit"
                class="rounded-md bg-primary px-5 py-2.5 text-sm text-primary-foreground hover:opacity-90 transition">
          {{ $isEdit ? 'Save Changes' : 'Create Workshop' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
