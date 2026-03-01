@extends('layouts.dashboard')

@php
  $title='Scholarship Details';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
  ];
@endphp

@section('content')
<div class="max-w-3xl space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">{{ $scholarship->title }}</h1>
      <p class="text-sm text-muted-foreground">Scholarship details</p>
    </div>
    <a href="{{ route('college.scholarships') }}" class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-6 space-y-4">
    <div class="grid md:grid-cols-2 gap-4 text-sm">
      <div><span class="text-muted-foreground">Deadline:</span> {{ $scholarship->deadline ?? '—' }}</div>
      <div><span class="text-muted-foreground">Amount:</span> {{ $scholarship->amount ?? '—' }}</div>
    </div>

    <div>
      <div class="font-semibold">Description</div>
      <div class="text-sm text-muted-foreground mt-2 whitespace-pre-line">
        {{ $scholarship->description ?? '—' }}
      </div>
    </div>
  </div>

</div>
@endsection
