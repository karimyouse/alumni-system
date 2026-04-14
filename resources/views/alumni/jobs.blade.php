@extends('layouts.dashboard')

@php
  $title = __('Job Opportunities');
  $role  = 'Alumni';

  $nav = [
    ['label' => 'Overview', 'href' => '/alumni', 'icon' => 'layout-dashboard'],
    ['label' => 'My Profile', 'href' => '/alumni/profile', 'icon' => 'user'],
    ['label' => 'Job Opportunities', 'href' => '/alumni/jobs', 'icon' => 'briefcase'],
    ['label' => 'Workshops', 'href' => '/alumni/workshops', 'icon' => 'calendar-days', 'badge' => $workshopBadgeCount ?? 0],
    ['label' => 'Scholarships', 'href' => '/alumni/scholarships', 'icon' => 'graduation-cap'],
    ['label' => 'Recommendations', 'href' => '/alumni/recommendations', 'icon' => 'send', 'badge' => $recommendationsReceived ?? 0],
    ['label' => 'Leaderboard', 'href' => '/alumni/leaderboard', 'icon' => 'trophy'],
    ['label' => 'My Applications', 'href' => '/alumni/applications', 'icon' => 'file-text', 'badge' => $applicationsBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold leading-tight">Job Opportunities</h1>
      <p class="text-sm text-muted-foreground sm:text-base">Find your next career opportunity</p>
    </div>

    <form method="GET" action="{{ route('alumni.jobs') }}" class="flex w-full items-center gap-2 sm:w-auto">
      <div class="relative min-w-0 flex-1 sm:w-64 sm:flex-none">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
        <input name="q" value="{{ $q }}" placeholder="Search jobs..."
               class="w-full rounded-md border border-input bg-background/60 pl-9 pr-3 py-2 text-sm" />
      </div>

      <button type="submit" class="inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-md border border-border hover:bg-accent/50">
        <i data-lucide="arrow-right" class="h-4 w-4"></i>
      </button>
    </form>
  </div>

  <div class="grid gap-4">
    @foreach($jobs as $job)
      @php
        $applied = in_array($job->id, $appliedJobIds);
        $companyProfile = $job->company?->companyProfile;
      @endphp

      <div class="rounded-xl border border-border bg-card overflow-hidden">
        <div class="p-4 sm:p-6">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="briefcase" class="h-5 w-5 text-primary"></i>
              </div>

              <div class="min-w-0 flex-1">
                <h3 class="text-lg font-semibold leading-snug break-words">{{ $job->title }}</h3>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                  <span class="break-words">{{ $companyProfile?->company_name ?? $job->company_name }}</span>
                  @if(($companyProfile?->status ?? null) === 'approved')
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-500/15 px-2 py-0.5 text-xs font-medium text-green-400">
                      <i data-lucide="badge-check" class="h-3 w-3"></i>
                      Verified
                    </span>
                  @endif
                  @if($companyProfile?->industry)
                    <span class="inline-flex rounded-full border border-border px-2 py-0.5 text-xs">
                      {{ $companyProfile->industry }}
                    </span>
                  @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-muted-foreground">
                  @if($job->location)
                    <span class="inline-flex max-w-full items-center gap-1 break-words">
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
                  <p class="text-sm text-muted-foreground mt-2 max-w-3xl break-words">
                    {{ $job->description }}
                  </p>
                @endif
              </div>
            </div>

            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:flex-wrap sm:items-center sm:justify-end lg:flex-nowrap">
              @if($job->salary)
                <span class="col-span-2 inline-flex min-h-9 items-center justify-center rounded-md bg-secondary px-3 py-2 text-xs font-medium sm:min-h-0 sm:rounded-full sm:py-1">
                  {{ $job->salary }}
                </span>
              @endif

              @php $saved = isset($savedJobIds) && in_array($job->id, $savedJobIds); @endphp
              <form method="POST" action="{{ route('alumni.jobs.save', $job) }}" class="min-w-0">
                @csrf
                <button type="submit"
                        class="w-full rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50 sm:w-auto">
                  {{ $saved ? 'Saved' : 'Save' }}
                </button>
              </form>

              @if($applied)
                <button type="button"
                        class="w-full rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground opacity-60 sm:w-auto"
                        disabled>
                  Applied
                </button>
              @else
                <a href="{{ route('alumni.jobs.show', $job) }}"
                   class="inline-flex w-full items-center justify-center rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
                  Review & Apply
                </a>
              @endif
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
