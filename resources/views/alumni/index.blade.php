@extends('layouts.dashboard')

@php
  $title = 'Alumni Dashboard';
  $role  = 'Alumni';


  $nav = [
    ['label' => 'Overview', 'href' => '/alumni'],
    ['label' => 'Profile', 'href' => '/alumni/profile'],
    ['label' => 'Jobs', 'href' => '/alumni/jobs', 'badge' => 12],
    ['label' => 'Workshops', 'href' => '/alumni/workshops', 'badge' => 3],
    ['label' => 'Scholarships', 'href' => '/alumni/scholarships'],
    ['label' => 'Recommendations', 'href' => '/alumni/recommendations'],
    ['label' => 'Leaderboard', 'href' => '/alumni/leaderboard'],
    ['label' => 'Applications', 'href' => '/alumni/applications'],
  ];

  $userName = auth()->user()->name ?? 'Alumni';


  $recentJobs = [
    ['id' => 1, 'title' => 'Frontend Developer', 'company' => 'TechCorp',   'location' => 'Gaza',     'type' => 'Full-time', 'posted' => '2 days ago'],
    ['id' => 2, 'title' => 'Software Engineer',  'company' => 'StartupX',   'location' => 'Remote',   'type' => 'Full-time', 'posted' => '3 days ago'],
    ['id' => 3, 'title' => 'UI/UX Designer',     'company' => 'DesignHub',  'location' => 'Ramallah', 'type' => 'Part-time', 'posted' => '5 days ago'],
  ];

  $upcomingWorkshops = [
    ['id' => 1, 'title' => 'Career Development Workshop', 'date' => 'Jan 15, 2025', 'time' => '10:00 AM'],
    ['id' => 2, 'title' => 'Technical Interview Prep',    'date' => 'Jan 20, 2025', 'time' => '2:00 PM'],
  ];

  $notifications = [
    ['id' => 1, 'message' => 'New job opportunity matches your profile', 'time' => '1 hour ago'],
    ['id' => 2, 'message' => 'Workshop registration deadline tomorrow',  'time' => '3 hours ago'],
    ['id' => 3, 'message' => 'You received a new recommendation',        'time' => '1 day ago'],
  ];

  $leaderboard = [
    ['rank' => 1, 'name' => 'Ahmed Hassan', 'points' => 1250, 'avatar' => 'AH'],
    ['rank' => 2, 'name' => 'Sara Ali',     'points' => 1180, 'avatar' => 'SA'],
    ['rank' => 3, 'name' => 'Omar Khalil',  'points' => 1050, 'avatar' => 'OK'],
  ];
@endphp

@section('content')
<div class="space-y-6">


  <div class="rounded-xl border border-primary/20 bg-gradient-to-r from-primary/10 via-primary/5 to-transparent">
    <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1">
          Welcome, {{ $userName }}!
        </h2>
        <p class="text-muted-foreground">
          Your profile is 75% complete. Add more details to stand out to employers.
        </p>
      </div>

      <a href="/alumni/profile" data-testid="button-complete-profile">
        <x-ui.button>
          Complete Profile
          <i data-lucide="arrow-right" class="h-4 w-4 ml-2"></i>
        </x-ui.button>
      </a>
    </div>
  </div>


  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Profile Views</div>
        <i data-lucide="user" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">48</div>
      <div class="mt-1 text-xs text-muted-foreground flex items-center gap-2">
        <span>This month</span>
        <span class="text-green-500">▲ 12%</span>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Job Applications</div>
        <i data-lucide="briefcase" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">5</div>
      <div class="mt-1 text-xs text-muted-foreground">Active applications</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Workshops Attended</div>
        <i data-lucide="calendar-days" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">8</div>
      <div class="mt-1 text-xs text-muted-foreground">This year</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Leaderboard Points</div>
        <i data-lucide="trophy" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">850</div>
      <div class="mt-1 text-xs text-muted-foreground flex items-center gap-2">
        <span>Rank #15</span>
        <span class="text-green-500">▲ 5%</span>
      </div>
    </div>

  </div>


  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 flex items-start justify-between gap-2 border-b border-border">
        <div>
          <div class="text-lg font-semibold">Jobs</div>
          <div class="text-sm text-muted-foreground">Latest opportunities for you</div>
        </div>
        <a href="/alumni/jobs" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          View all
          <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @foreach($recentJobs as $job)
          <div class="flex items-center gap-4 p-4 rounded-lg bg-accent/40">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
              <i data-lucide="building-2" class="h-5 w-5"></i>
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="font-medium">{{ $job['title'] }}</h4>
              <p class="text-sm text-muted-foreground">{{ $job['company'] }}</p>

              <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="inline-flex items-center rounded-full bg-secondary px-2 py-1 text-xs">
                  <i data-lucide="map-pin" class="h-3 w-3 mr-1"></i>
                  {{ $job['location'] }}
                </span>

                <span class="inline-flex items-center rounded-full border border-border px-2 py-1 text-xs">
                  {{ $job['type'] }}
                </span>

                <span class="text-xs text-muted-foreground inline-flex items-center">
                  <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                  {{ $job['posted'] }}
                </span>
              </div>
            </div>

            <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/60 transition">
              Apply
            </button>
          </div>
        @endforeach
      </div>
    </div>


    <div class="space-y-6">


      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 flex items-center justify-between gap-2 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="bell" class="h-4 w-4"></i>
            Notifications
          </div>
          <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">
            {{ count($notifications) }}
          </span>
        </div>

        <div class="p-6 space-y-3">
          @foreach($notifications as $n)
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
              <div class="flex-1">
                <p class="text-sm">{{ $n['message'] }}</p>
                <p class="text-xs text-muted-foreground">{{ $n['time'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>


      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="calendar-days" class="h-4 w-4"></i>
            Upcoming Workshops
          </div>
        </div>

        <div class="p-6 space-y-3">
          @foreach($upcomingWorkshops as $w)
            <div class="p-3 rounded-lg bg-accent/50">
              <p class="font-medium text-sm">{{ $w['title'] }}</p>
              <p class="text-xs text-muted-foreground mt-1">
                {{ $w['date'] }} at {{ $w['time'] }}
              </p>
            </div>
          @endforeach
        </div>
      </div>

      
      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 flex items-center justify-between gap-2 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="trophy" class="h-4 w-4"></i>
            Leaderboard
          </div>
          <a href="/alumni/leaderboard" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
            View all
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
          </a>
        </div>

        <div class="p-6 space-y-3">
          @foreach($leaderboard as $entry)
            <div class="flex items-center gap-3 p-3 rounded-lg bg-accent/40">
              <div class="w-8 text-sm font-semibold text-muted-foreground">#{{ $entry['rank'] }}</div>

              <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center font-semibold text-primary">
                {{ $entry['avatar'] }}
              </div>

              <div class="flex-1">
                <p class="text-sm font-medium">{{ $entry['name'] }}</p>
              </div>

              <span class="text-sm font-semibold text-primary">{{ $entry['points'] }}</span>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
