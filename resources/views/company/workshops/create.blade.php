@extends('layouts.dashboard')

@php
  $title='Propose Workshop';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];
@endphp

@section('content')
<div class="max-w-2xl space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Propose Workshop</h1>
    <p class="text-sm text-muted-foreground">Submit a new workshop proposal</p>
  </div>

  <form method="POST" action="{{ route('company.workshops.store') }}"
        class="rounded-xl border border-border bg-card p-6 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-medium">Title</label>
      <input name="title" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             placeholder="Tech Career Day 2026" value="{{ old('title') }}">
      @error('title') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="grid md:grid-cols-2 gap-3">
      <div>
        <label class="text-sm font-medium">Date</label>
        <input name="date" required
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Feb 15, 2026" value="{{ old('date') }}">
        @error('date') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium">Time</label>
        <input name="time" required
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="10:00 AM - 2:00 PM" value="{{ old('time') }}">
        @error('time') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Location</label>
      <input name="location" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             placeholder="Main Campus" value="{{ old('location') }}">
      @error('location') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
  <label class="text-sm font-medium">Capacity (optional)</label>
  <input name="capacity" type="number" min="1"
         class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
         placeholder="e.g. 50" value="{{ old('capacity') }}">
</div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Submit
      </button>

      <a href="{{ route('company.workshops') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Cancel
      </a>
    </div>
  </form>

</div>
@endsection
