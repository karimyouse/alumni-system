@extends('layouts.dashboard')

@php
  $title = $isEdit ? __('Edit Scholarship') : __('Add Scholarship');
  $role='College';

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
<div class="max-w-2xl space-y-6">

  <div>
    <h1 class="text-2xl font-bold">{{ $isEdit ? 'Edit Scholarship' : 'Add Scholarship' }}</h1>
    <p class="text-sm text-muted-foreground">
      {{ $isEdit ? 'Update scholarship details' : 'Create a scholarship for alumni' }}
    </p>
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

  <form method="POST"
        action="{{ $isEdit ? route('college.scholarships.update', $scholarship) : route('college.scholarships.store') }}"
        class="rounded-xl border border-border bg-card p-6 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-medium">Title *</label>
      <input name="title" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             value="{{ old('title', $scholarship->title ?? '') }}"
             placeholder="Graduate Excellence Award">
    </div>

    <div class="grid md:grid-cols-2 gap-3">
      <div>
        <label class="text-sm font-medium">Deadline</label>
        <input name="deadline"
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               value="{{ old('deadline', $scholarship->deadline ?? '') }}"
               placeholder="Feb 15, 2026">
      </div>
      <div>
        <label class="text-sm font-medium">Amount</label>
        <input name="amount"
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               value="{{ old('amount', $scholarship->amount ?? '') }}"
               placeholder="$5,000">
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Description</label>
      <textarea name="description" rows="5"
                class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                placeholder="Scholarship details...">{{ old('description', $scholarship->description ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        {{ $isEdit ? 'Save Changes' : 'Create Scholarship' }}
      </button>
      <a href="{{ route('college.scholarships') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Cancel
      </a>
    </div>
  </form>

</div>
@endsection
