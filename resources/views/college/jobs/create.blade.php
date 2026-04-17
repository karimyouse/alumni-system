@extends('layouts.dashboard')

@php
  $title = $isEdit ? __('Edit Job') : __('Post Job');
  $role = 'College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Browse Alumni','href'=>'/college/alumni','icon'=>'users'],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days'],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6 max-w-3xl">

  <div class="flex items-center justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">{{ $isEdit ? 'Edit Job' : 'Post Job' }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ $isEdit ? 'Update a college job posting' : 'Create a new college job posting' }}
      </p>
    </div>

    <a href="{{ route('college.jobs') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition">
      Back
    </a>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
      <div class="font-semibold text-destructive mb-1">Fix the following:</div>
      <ul class="list-disc pl-5 space-y-1 text-destructive/90">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-xl border border-border bg-card p-6">
    <form method="POST"
          action="{{ $isEdit ? route('college.jobs.update', $job) : route('college.jobs.store') }}"
          class="space-y-4">
      @csrf

      <div class="space-y-2">
        <label class="text-sm font-medium">Job Title *</label>
        <input name="title"
               value="{{ old('title', $job->title ?? '') }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Frontend Developer"
               required>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Organization Name</label>
        <input name="company_name"
               value="{{ old('company_name', $job->company_name ?? ($companyName ?? 'PTC College')) }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="PTC College">
        <p class="text-xs text-muted-foreground">
          Leave it as PTC College unless you want a custom display name.
        </p>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">Location</label>
          <input name="location"
                 value="{{ old('location', $job->location ?? '') }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                 placeholder="Gaza / Remote">
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Type</label>
          <input name="type"
                 value="{{ old('type', $job->type ?? '') }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                 placeholder="Full-time / Part-time">
        </div>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Salary</label>
        <input name="salary"
               value="{{ old('salary', $job->salary ?? '') }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="$800-$1200">
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Description</label>
        <textarea name="description"
                  rows="6"
                  class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                  placeholder="Job responsibilities, requirements, qualifications...">{{ old('description', $job->description ?? '') }}</textarea>
      </div>

      <div class="pt-2">
        <button type="submit"
                class="rounded-md bg-primary px-5 py-2 text-sm text-primary-foreground hover:opacity-90 transition">
          {{ $isEdit ? 'Save Changes' : 'Publish Job' }}
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
