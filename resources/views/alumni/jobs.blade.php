@extends('layouts.dashboard')

@php
  $title = 'Job Opportunities';
  $role  = 'Alumni';

  $jobsBadge = $jobs->total();

  $nav = [
    ['label' => 'Overview', 'href' => '/alumni', 'icon' => 'layout-dashboard'],
    ['label' => 'My Profile', 'href' => '/alumni/profile', 'icon' => 'user'],
    ['label' => 'Job Opportunities', 'href' => '/alumni/jobs', 'icon' => 'briefcase', 'badge' => $jobsBadge],
    ['label' => 'Workshops', 'href' => '/alumni/workshops', 'icon' => 'calendar-days'],
    ['label' => 'Scholarships', 'href' => '/alumni/scholarships', 'icon' => 'graduation-cap'],
    ['label' => 'Recommendations', 'href' => '/alumni/recommendations', 'icon' => 'send'],
    ['label' => 'Leaderboard', 'href' => '/alumni/leaderboard', 'icon' => 'trophy'],
    ['label' => 'My Applications', 'href' => '/alumni/applications', 'icon' => 'file-text'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">{{ __('Job Opportunities') }}</h1>
      <p class="text-muted-foreground">{{ __('Find your next career opportunity') }}</p>
    </div>

    <form method="GET" action="{{ route('alumni.jobs') }}" class="flex items-center gap-2">
      <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
        <input name="q" value="{{ $q }}" placeholder="{{ __('Search jobs...') }}"
               class="w-64 rounded-md border border-input bg-background/60 pl-9 pr-3 py-2 text-sm" />
      </div>

      <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50">
        <i data-lucide="{{ app()->getLocale() === 'ar' ? 'arrow-left' : 'arrow-right' }}" class="h-4 w-4"></i>
      </button>
    </form>
  </div>

  <div class="grid gap-4">
    @foreach($jobs as $job)
      @php
        $applied = in_array($job->id, $appliedJobIds);
        $saved = isset($savedJobIds) && in_array($job->id, $savedJobIds);
      @endphp

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6">
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="briefcase" class="h-5 w-5 text-primary"></i>
              </div>

              <div>
                <h3 class="text-lg font-semibold">{{ $job->title }}</h3>
                <p class="text-sm text-muted-foreground">{{ $job->company_name }}</p>

                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-muted-foreground">
                  @if($job->location)
                    <span class="inline-flex items-center gap-1">
                      <i data-lucide="map-pin" class="h-3 w-3"></i> {{ $job->location }}
                    </span>
                  @endif
                  @if($job->type)
                    <span class="inline-flex items-center rounded-full border border-border px-2 py-0.5">{{ $job->type }}</span>
                  @endif
                  @if($job->posted)
                    <span class="inline-flex items-center gap-1">
                      <i data-lucide="clock" class="h-3 w-3"></i> {{ $job->posted }}
                    </span>
                  @endif
                </div>

                @if($job->description)
                  <p class="text-sm text-muted-foreground mt-2 max-w-3xl">
                    {{ $job->description }}
                  </p>
                @endif
              </div>
            </div>

            <div class="flex items-center gap-2">
              @if($job->salary)
                <span class="text-xs rounded-full bg-secondary px-3 py-1">{{ $job->salary }}</span>
              @endif

              <form method="POST" action="{{ route('alumni.jobs.save', $job) }}">
                @csrf
                <button type="submit"
                        class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                  {{ $saved ? __('Saved') : __('Save') }}
                </button>
              </form>

              <form method="POST" action="{{ route('alumni.jobs.apply', $job) }}">
                @csrf
                <button type="submit"
                        class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90"
                        {{ $applied ? 'disabled' : '' }}>
                  {{ $applied ? __('Applied') : __('Apply Now') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div>
    {{ $jobs->links() }}
  </div>

</div>
@endsection
