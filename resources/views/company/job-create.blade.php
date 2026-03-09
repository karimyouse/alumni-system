@extends('layouts.dashboard')

@php
  $title = $isEdit ? 'Edit Job' : 'Post New Job';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];
@endphp

@section('content')
<div class="space-y-6 max-w-3xl">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">{{ $isEdit ? 'Edit Job' : 'Post New Job' }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ $isEdit ? 'Update your job posting details' : 'Create a job post visible to alumni' }}
      </p>
    </div>

    <a href="{{ route('company.jobs') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
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
          action="{{ $isEdit ? route('company.jobs.update', $job) : route('company.jobs.store') }}"
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
        <label class="text-sm font-medium">Company Name *</label>
        <input name="company_name"
               value="{{ old('company_name', $job->company_name ?? ($companyName ?? '')) }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               required>
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
        <textarea name="description" rows="5"
                  class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                  placeholder="Job responsibilities, requirements...">{{ old('description', $job->description ?? '') }}</textarea>
      </div>

      <button class="rounded-md bg-primary px-5 py-2 text-sm text-primary-foreground hover:opacity-90">
        {{ $isEdit ? 'Save Changes' : 'Publish Job' }}
      </button>
    </form>
  </div>

</div>
@endsection
