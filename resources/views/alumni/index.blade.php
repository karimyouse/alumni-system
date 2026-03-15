@extends('layouts.dashboard')

@php
  $title = __('Alumni Dashboard');
  $role  = __('Alumni');

  $nav = [
    ['label'=>__('Overview'),'href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>__('My Profile'),'href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>__('Job Opportunities'),'href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>__('Workshops'),'href'=>'/alumni/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>__('Scholarships'),'href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>__('Recommendations'),'href'=>'/alumni/recommendations','icon'=>'message-square','badge'=>$recommendationsReceived ?? 0],
    ['label'=>__('Leaderboard'),'href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>__('My Applications'),'href'=>'/alumni/applications','icon'=>'file-text','badge'=>$applicationsBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="rounded-xl border border-primary/20 bg-gradient-to-r from-primary/10 via-primary/5 to-transparent">
    <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1">{{ __("Welcome, :name!", ["name" => $userName]) }}</h2>
        <p class="text-muted-foreground">
          {{ __("Your profile is :percent% complete. Add more details to stand out to employers.", ["percent" => $profileCompletion ?? 0]) }}
        </p>
      </div>

      <a href="/alumni/profile">
        <x-ui.button>
          {{ __("Complete Profile") }}
          <i data-lucide="arrow-right" class="h-4 w-4 ml-2"></i>
        </x-ui.button>
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">{{ __("Profile Views") }}</div>
        <i data-lucide="user" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($profileViews ?? 0) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">
        {{ ($profileViews ?? 0) > 0 ? __('Tracked profile views') : __('No tracking data yet') }}
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">{{ __("Job Applications") }}</div>
        <i data-lucide="briefcase" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-2">{{ number_format($jobApplicationsCount ?? 0) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">{{ __("Applications submitted") }}</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">{{ __("Workshops Attended") }}</div>
        <i data-lucide="calendar-days" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-2">{{ number_format($workshopsCount ?? 0) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">{{ __("Registered workshops") }}</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">{{ __("Leaderboard Points") }}</div>
        <i data-lucide="trophy" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($leaderboardPoints ?? 0) }}</div>
      <div class="mt-1 text-xs text-muted-foreground">
        {{ __("Rank #:rank • :count activities", ["rank" => $leaderboardRank ?? "-", "count" => $leaderboardActivities ?? 0]) }}
      </div>
    </div>

  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 flex items-start justify-between gap-2 border-b border-border">
        <div>
          <div class="text-lg font-semibold">{{ __("Jobs") }}</div>
          <div class="text-sm text-muted-foreground">{{ __("Latest opportunities for you") }}</div>
        </div>
        <a href="/alumni/jobs" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          {{ __("View all") }} <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @forelse($recentJobs as $job)
          <div class="flex items-center gap-4 p-4 rounded-lg bg-accent/40">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
              <i data-lucide="building-2" class="h-5 w-5"></i>
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="font-medium">{{ $job->title }}</h4>
              <p class="text-sm text-muted-foreground">{{ $job->company_name }}</p>

              <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="inline-flex items-center rounded-full bg-secondary px-2 py-1 text-xs">
                  <i data-lucide="map-pin" class="h-3 w-3 mr-1"></i>
                  {{ $job->location ?? '-' }}
                </span>
                <span class="inline-flex items-center rounded-full border border-border px-2 py-1 text-xs">
                  {{ $job->type ?? '-' }}
                </span>
                <span class="text-xs text-muted-foreground inline-flex items-center">
                  <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                  {{ $job->posted_text ?? '-' }}
                </span>
              </div>
            </div>

            <a href="/alumni/jobs" class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/60 transition">
              {{ __("Apply") }}
            </a>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">{{ __("No jobs yet.") }}</div>
        @endforelse
      </div>
    </div>

    <div class="space-y-6">

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 flex items-center justify-between gap-2 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="bell" class="h-4 w-4"></i> {{ __("Notifications") }}
          </div>
          <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">
            {{ count($notifications ?? []) }}
          </span>
        </div>

        <div class="p-6 space-y-3">
          @foreach($notifications as $n)
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
              <div class="flex-1">
                <p class="text-sm">{{ $n['message'] }}</p>
                @if(!empty($n['time']))
                  <p class="text-xs text-muted-foreground">{{ $n['time'] }}</p>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="calendar-days" class="h-4 w-4"></i> {{ __("Upcoming Workshops") }}
          </div>
        </div>

        <div class="p-6 space-y-3">
          @forelse($upcomingWorkshops as $w)
            <div class="p-3 rounded-lg bg-accent/50">
              <p class="font-medium text-sm">{{ $w->title }}</p>
              <p class="text-xs text-muted-foreground mt-1">
                {{ $w->date ?? '-' }} {{ __('at') }} {{ $w->time ?? '-' }}
              </p>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">{{ __("No workshops yet.") }}</div>
          @endforelse
        </div>
      </div>

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 flex items-center justify-between gap-2 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="trophy" class="h-4 w-4"></i> {{ __("Leaderboard") }}
          </div>
          <a href="/alumni/leaderboard" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
            {{ __("View all") }} <i data-lucide="arrow-right" class="h-4 w-4"></i>
          </a>
        </div>

        <div class="p-6 space-y-3">
          @forelse($topLeaderboard as $entry)
            <div class="flex items-center gap-3 p-3 rounded-lg bg-accent/40">
              <div class="w-8 text-sm font-semibold text-muted-foreground">#{{ $entry['rank'] }}</div>
              <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center font-semibold text-primary">
                {{ $entry['avatar'] }}
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium">{{ $entry['name'] }}</p>
              </div>
              <span class="text-sm font-semibold text-primary">{{ number_format($entry['points']) }}</span>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">{{ __("No leaderboard data yet.") }}</div>
          @endforelse
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
