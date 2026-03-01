@extends('layouts.dashboard')

@php
  $title='Add Scholarship';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
  ];
@endphp

@section('content')
<div class="max-w-2xl space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Add Scholarship</h1>
    <p class="text-sm text-muted-foreground">Publish a scholarship for alumni</p>
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

  <form method="POST" action="{{ route('college.scholarships.store') }}"
        class="rounded-xl border border-border bg-card p-6 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-medium">Title *</label>
      <input name="title" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             value="{{ old('title') }}" placeholder="Merit Scholarship 2026">
    </div>

    <div class="grid md:grid-cols-2 gap-3">
      <div>
        <label class="text-sm font-medium">Deadline</label>
        <input name="deadline"
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               value="{{ old('deadline') }}" placeholder="Mar 10, 2026">
      </div>
      <div>
        <label class="text-sm font-medium">Amount</label>
        <input name="amount"
               class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               value="{{ old('amount') }}" placeholder="$2,000">
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Description</label>
      <textarea name="description" rows="5"
                class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                placeholder="Scholarship details...">{{ old('description') }}</textarea>
    </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Create
      </button>
      <a href="{{ route('college.scholarships') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Cancel
      </a>
    </div>
  </form>

</div>
@endsection
