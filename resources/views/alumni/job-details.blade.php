@extends('layouts.dashboard')

@php
  $title = __('Job Details');
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

  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold leading-tight break-words">{{ $job->title }}</h1>
      <p class="text-sm text-muted-foreground break-words">
        {{ $job->company_name ?? 'Company' }}
        @if($job->location) • {{ $job->location }} @endif
        @if($job->type) • {{ $job->type }} @endif
      </p>
    </div>

    <a href="{{ route('alumni.jobs') }}"
       class="inline-flex w-full items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-4 space-y-4 sm:p-6">
    <div class="grid md:grid-cols-3 gap-4 text-sm">
      <div>
        <div class="text-muted-foreground">Company</div>
        <div class="font-semibold">{{ $job->company_name ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">Location</div>
        <div class="font-semibold">{{ $job->location ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">Salary</div>
        <div class="font-semibold">{{ $job->salary ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">Type</div>
        <div class="font-semibold">{{ $job->type ?? '—' }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">Posted</div>
        <div class="font-semibold">{{ $job->posted ?? ($job->created_at?->format('M d, Y') ?? '—') }}</div>
      </div>

      <div>
        <div class="text-muted-foreground">Status</div>
        <div class="font-semibold">{{ $job->status ?? '—' }}</div>
      </div>
    </div>

    <div>
      <div class="font-semibold mb-2">Description</div>
      <div class="text-sm text-muted-foreground whitespace-pre-line break-words">
        {{ $job->description ?? '—' }}
      </div>
    </div>

  </div>

  @if($job->company || $job->company_name)
    @include('partials.company-trust-card', [
      'company' => $job->company,
      'fallbackName' => $job->company_name,
    ])
  @endif

  <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="text-sm text-muted-foreground">
        Review the company details above before sending your application.
      </div>

      <div class="grid grid-cols-1 gap-2 sm:flex sm:flex-wrap sm:justify-end">
        @if($isApplied)
          <button class="w-full rounded-md bg-secondary px-4 py-2 text-sm text-secondary-foreground cursor-not-allowed sm:w-auto" disabled>
            Applied
          </button>
        @else
          <form method="POST" action="{{ route('alumni.jobs.apply', $job) }}">
            @csrf
            <button class="w-full rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
              Apply Now
            </button>
          </form>
        @endif

        <form method="POST" action="{{ route('alumni.jobs.save', $job) }}">
          @csrf
          <button class="w-full rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
            {{ $isSaved ? 'Unsave' : 'Save Job' }}
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
