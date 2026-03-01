@extends('layouts.dashboard')

@php
  $title='New Announcement';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
  ];
@endphp

@section('content')
<div class="max-w-2xl space-y-6">

  <div>
    <h1 class="text-2xl font-bold">New Announcement</h1>
    <p class="text-sm text-muted-foreground">Publish an announcement to the selected audience</p>
  </div>

  @if($errors->any())
    <div class="rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
      <div class="font-semibold text-destructive mb-1">Fix the following:</div>
      <ul class="list-disc pl-5 space-y-1 text-destructive/90">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('college.announcements.store') }}"
        class="rounded-xl border border-border bg-card p-6 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-medium">Title *</label>
      <input name="title" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             value="{{ old('title') }}" placeholder="Important Notice">
    </div>

    <div>
      <label class="text-sm font-medium">Audience *</label>
      <select name="audience" required class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
        <option value="all" {{ old('audience')==='all'?'selected':'' }}>All users</option>
        <option value="alumni" {{ old('audience')==='alumni'?'selected':'' }}>Alumni</option>
        <option value="company" {{ old('audience')==='company'?'selected':'' }}>Companies</option>
        <option value="college" {{ old('audience')==='college'?'selected':'' }}>College</option>
      </select>
    </div>

    <div>
      <label class="text-sm font-medium">Message *</label>
      <textarea name="body" rows="6" required
                class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                placeholder="Write your announcement...">{{ old('body') }}</textarea>
    </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Publish
      </button>
      <a href="{{ route('college.announcements') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Cancel
      </a>
    </div>
  </form>

</div>
@endsection
