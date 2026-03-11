@extends('layouts.dashboard')

@php
  $title='Job Details';
  $role='Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase'],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days'],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];
@endphp

@section('content')
<div class="space-y-6 max-w-4xl">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">{{ $job->title }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ $job->company_name ?? __('Company') }}
        @if($job->location) • {{ $job->location }} @endif
        @if($job->type) • {{ $job->type }} @endif
      </p>
    </div>

    <a href="{{ route('alumni.jobs') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
      {{ __('Back') }}
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-6 space-y-4">
    <div class="grid md:grid-cols-3 gap-4 text-sm">
      <div>
        <div class="text-muted-foreground">{{ __('Company') }}</div>
        <div class="font-semibold">{{ $job->company_name ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">{{ __('Location') }}</div>
        <div class="font-semibold">{{ $job->location ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">{{ __('Salary') }}</div>
        <div class="font-semibold">{{ $job->salary ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">{{ __('Type') }}</div>
        <div class="font-semibold">{{ $job->type ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">{{ __('Posted') }}</div>
        <div class="font-semibold">{{ $job->posted ?? ($job->created_at?->format('M d, Y') ?? '—') }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">{{ __('Status') }}</div>
        <div class="font-semibold">{{ __($job->status ?? '—') }}</div>
      </div>
    </div>

    <div>
      <div class="font-semibold mb-2">{{ __('Description') }}</div>
      <div class="text-sm text-muted-foreground whitespace-pre-line">
        {{ $job->description ?? '—' }}
      </div>
    </div>

    <div class="flex flex-wrap gap-2 pt-2">
      @if($isApplied)
        <button class="rounded-md bg-secondary px-4 py-2 text-sm text-secondary-foreground cursor-not-allowed" disabled>
          {{ __('Applied') }}
        </button>
      @else
        <form method="POST" action="{{ route('alumni.jobs.apply', $job) }}">
          @csrf
          <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
            {{ __('Apply Now') }}
          </button>
        </form>
      @endif

      <form method="POST" action="{{ route('alumni.jobs.save', $job) }}">
        @csrf
        <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          {{ $isSaved ? __('Unsave') : __('Save Job') }}
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
