@extends('layouts.dashboard')

@php
  $title = 'College Dashboard';
  $role  = 'College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Alumni','href'=>'/college/alumni','icon'=>'users'],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days'],
    ['label'=>'Jobs','href'=>'/college/jobs','icon'=>'briefcase'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];

  $userName = auth()->user()->name ?? 'College';

  $recentAlumni = [
    ['id'=>'1','name'=>'Ahmed Hassan','department'=>'Computer Science','year'=>2024,'status'=>'Employed'],
    ['id'=>'2','name'=>'Sara Ali','department'=>'Information Technology','year'=>2024,'status'=>'Job Seeking'],
    ['id'=>'3','name'=>'Omar Khalil','department'=>'Computer Science','year'=>2023,'status'=>'Employed'],
    ['id'=>'4','name'=>'Mona Ibrahim','department'=>'Web Development','year'=>2024,'status'=>'Further Study'],
  ];

  $upcomingEvents = [
    ['id'=>'1','title'=>'Career Fair 2025','date'=>'Jan 25, 2025','type'=>'Event','registered'=>45],
    ['id'=>'2','title'=>'Resume Writing Workshop','date'=>'Jan 18, 2025','type'=>'Workshop','registered'=>28],
    ['id'=>'3','title'=>'Industry Speaker Series','date'=>'Jan 30, 2025','type'=>'Seminar','registered'=>62],
  ];

  $departmentStats = [
    ['name'=>'Computer Science','alumni'=>450,'employed'=>85],
    ['name'=>'Information Technology','alumni'=>320,'employed'=>78],
    ['name'=>'Web Development','alumni'=>180,'employed'=>82],
    ['name'=>'Networking','alumni'=>150,'employed'=>75],
  ];
@endphp

@section('content')
<div class="space-y-6">


  <div class="rounded-xl border border-green-500/20 bg-gradient-to-r from-green-500/10 via-green-500/5 to-transparent">
    <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1">Welcome, {{ $userName }}!</h2>
        <p class="text-muted-foreground">
          Manage alumni relations and track graduate success from your dashboard.
        </p>
      </div>

      <div class="flex gap-2">
        <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition"
                data-testid="button-add-workshop">
          <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
          Add Workshop
        </button>

        <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 transition"
                data-testid="button-post-job">
          <i data-lucide="briefcase" class="h-4 w-4 mr-2 inline"></i>
          Post Job
        </button>
      </div>
    </div>
  </div>


  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Alumni</div>
        <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">2,547</div>
      <div class="text-xs text-muted-foreground mt-1">Registered graduates</div>
      <div class="text-xs text-green-500 mt-1">▲ 8%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Employment Rate</div>
        <i data-lucide="user-check" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">82%</div>
      <div class="text-xs text-muted-foreground mt-1">Of registered alumni</div>
      <div class="text-xs text-green-500 mt-1">▲ 3%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Active Job Posts</div>
        <i data-lucide="briefcase" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">24</div>
      <div class="text-xs text-muted-foreground mt-1">From partner companies</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Upcoming Events</div>
        <i data-lucide="calendar-days" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">5</div>
      <div class="text-xs text-muted-foreground mt-1">This month</div>
    </div>
  </div>


  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">Recent Alumni</div>
          <div class="text-sm text-muted-foreground">Latest registered graduates</div>
        </div>

        <a href="/college/alumni"
           class="text-sm text-primary hover:underline inline-flex items-center gap-1"
           data-testid="button-view-all-alumni">
          View All <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @foreach($recentAlumni as $a)
          @php
            $initials = collect(explode(' ', $a['name']))->map(fn($n)=>mb_substr($n,0,1))->join('');
            $employed = $a['status'] === 'Employed';
          @endphp

          <div class="flex items-center gap-4 p-4 rounded-lg border border-border hover:shadow-sm transition-all"
               data-testid="card-alumni-{{ $a['id'] }}">
            <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
              {{ $initials }}
            </div>

            <div class="flex-1 min-w-0">
              <div class="font-medium">{{ $a['name'] }}</div>
              <div class="text-sm text-muted-foreground">
                {{ $a['department'] }} • Class of {{ $a['year'] }}
              </div>
            </div>

            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs
              {{ $employed ? 'bg-green-500/10 text-green-400' : 'bg-secondary text-secondary-foreground' }}">
              {{ $a['status'] }}
            </span>
          </div>
        @endforeach
      </div>
    </div>


    <div class="space-y-6">


      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border flex items-center justify-between">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="calendar-days" class="h-4 w-4"></i>
            Upcoming Events
          </div>
          <a href="/college/workshops" class="text-sm text-primary hover:underline">Manage</a>
        </div>

        <div class="p-6 space-y-4">
          @foreach($upcomingEvents as $e)
            <div class="p-3 rounded-lg bg-accent/50">
              <div class="flex items-start justify-between gap-2 mb-2">
                <p class="font-medium text-sm">{{ $e['title'] }}</p>
                <span class="inline-flex items-center rounded-full border border-border px-2 py-1 text-xs">
                  {{ $e['type'] }}
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-muted-foreground">
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  {{ $e['date'] }}
                </span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="users" class="h-3 w-3"></i>
                  {{ $e['registered'] }} registered
                </span>
              </div>
            </div>
          @endforeach
        </div>
      </div>


      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="trending-up" class="h-4 w-4"></i>
            Employment by Department
          </div>
        </div>

        <div class="p-6 space-y-4">
          @foreach($departmentStats as $d)
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm">{{ $d['name'] }}</span>
                <span class="text-sm font-medium">{{ $d['employed'] }}%</span>
              </div>
              <div class="h-2 rounded-full bg-muted overflow-hidden">
                <div class="h-2 rounded-full bg-primary" style="width: {{ $d['employed'] }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  
  <div class="rounded-xl border border-border bg-card">
    <div class="p-6 border-b border-border">
      <div class="text-lg font-semibold">Quick Actions</div>
      <div class="text-sm text-muted-foreground">Common tasks for managing alumni relations</div>
    </div>

    <div class="p-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
                data-testid="button-quick-workshop">
          <i data-lucide="calendar-days" class="h-5 w-5"></i>
          <span class="text-sm">Add Workshop</span>
        </button>

        <button class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
                data-testid="button-quick-announcement">
          <i data-lucide="megaphone" class="h-5 w-5"></i>
          <span class="text-sm">New Announcement</span>
        </button>

        <button class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
                data-testid="button-quick-scholarship">
          <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          <span class="text-sm">Add Scholarship</span>
        </button>

        <button class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
                data-testid="button-quick-story">
          <i data-lucide="award" class="h-5 w-5"></i>
          <span class="text-sm">Share Success Story</span>
        </button>
      </div>
    </div>
  </div>

</div>
@endsection
