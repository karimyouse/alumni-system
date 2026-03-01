@extends('layouts.dashboard')

@php
  $title = 'Add Workshop';
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
<div class="space-y-6 max-w-3xl">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Add Workshop</h1>
      <p class="text-sm text-muted-foreground">Create a new workshop for alumni</p>
    </div>



  </div>

  <div class="rounded-xl border border-border bg-card p-6">
    <form method="POST" action="{{ route('college.workshops.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="text-xs font-medium">Title <span class="text-destructive">*</span></label>
        <input name="title" value="{{ old('title') }}" required
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
               placeholder="Career Development Workshop">
        @error('title') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-medium">Date <span class="text-destructive">*</span></label>
          <input name="date" value="{{ old('date') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="Feb 15, 2026">
          @error('date') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-xs font-medium">Time <span class="text-destructive">*</span></label>
          <input name="time" value="{{ old('time') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="10:00 AM - 2:00 PM">
          @error('time') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-medium">Location <span class="text-destructive">*</span></label>
          <input name="location" value="{{ old('location') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="Main Campus, Hall A">
          @error('location') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-xs font-medium">Capacity (optional)</label>
          <input name="capacity" value="{{ old('capacity') }}"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="45">
          <div class="text-xs text-muted-foreground mt-1">Leave empty for unlimited seats.</div>
          @error('capacity') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <button type="submit"
              class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        Create Workshop
      </button>
    </form>
  </div>
</div>
@endsection
