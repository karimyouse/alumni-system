@extends('layouts.dashboard')

@php
  $title = __('College Dashboard');
  $role  = 'College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Manage Alumni','href'=>'/college/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone','badge'=>$announcementBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap','badge'=>$scholarshipBadgeCount ?? 0],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award','badge'=>$successStoryBadgeCount ?? 0],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];

  $userName = auth()->user()->name ?? 'College';
  $departmentStats = $departmentStats ?? [];
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

      <div class="flex gap-2 flex-wrap">
        <a href="{{ route('college.workshops.create') }}"
           class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition inline-flex items-center"
           data-testid="button-add-workshop">
          <i data-lucide="calendar-days" class="h-4 w-4 mr-2 inline"></i>
          Add Workshop
        </a>

        <a href="{{ route('college.jobs.create') }}"
           class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 transition inline-flex items-center"
           data-testid="button-post-job">
          <i data-lucide="briefcase" class="h-4 w-4 mr-2 inline"></i>
          Post Job
        </a>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Alumni</div>
        <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($totalAlumni) }}</div>
      <div class="text-xs text-muted-foreground mt-1">Registered graduates</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Employment Rate</div>
        <i data-lucide="user-check" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ $employmentRate }}%</div>
      <div class="text-xs text-muted-foreground mt-1">Of registered alumni</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Active Job Posts</div>
        <i data-lucide="briefcase" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($activeJobPosts) }}</div>
      <div class="text-xs text-muted-foreground mt-1">
        From partner companies and college posts
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Upcoming Events</div>
        <i data-lucide="calendar-days" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">{{ number_format($upcomingCount) }}</div>
      <div class="text-xs text-muted-foreground mt-1">Upcoming workshops</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">Recent Alumni</div>
          <div class="text-sm text-muted-foreground">Latest registered graduates</div>
        </div>

        <a href="{{ route('college.alumni') }}"
           class="text-sm text-primary hover:underline inline-flex items-center gap-1"
           data-testid="button-view-all-alumni">
          View All <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @forelse($recentAlumni as $a)
          @php
            $initials = collect(explode(' ', $a->name))->map(fn($n)=>mb_substr($n,0,1))->join('');
            $major = $a->alumniProfile?->major ?? '—';
            $year = $a->alumniProfile?->graduation_year ?? '—';
            $status = $a->alumniProfile?->employment_status ?? '—';
            $employed = strtolower($status) === 'employed';
          @endphp

          <div class="flex items-center gap-4 p-4 rounded-lg border border-border hover:shadow-sm transition-all"
               data-testid="card-alumni-{{ $a->id }}">
            <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
              {{ $initials }}
            </div>

            <div class="flex-1 min-w-0">
              <div class="font-medium">{{ $a->name }}</div>
              <div class="text-sm text-muted-foreground">
                {{ $major }} • Class of {{ $year }}
              </div>
            </div>

            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs
              {{ $employed ? 'bg-green-500/10 text-green-400' : 'bg-secondary text-secondary-foreground' }}">
              {{ $status }}
            </span>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">No alumni found.</div>
        @endforelse
      </div>
    </div>

    <div class="space-y-6">

      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border flex items-center justify-between">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="calendar-days" class="h-4 w-4"></i>
            Upcoming Events
          </div>
          <a href="{{ route('college.workshops') }}" class="text-sm text-primary hover:underline">Manage</a>
        </div>

        <div class="p-6 space-y-4">
          @forelse($upcomingEvents as $e)
            <div class="p-3 rounded-lg bg-accent/50">
              <div class="flex items-start justify-between gap-2 mb-2">
                <p class="font-medium text-sm">{{ $e->title }}</p>
                <span class="inline-flex items-center rounded-full border border-border px-2 py-1 text-xs">
                  Workshop
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-muted-foreground">
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  {{ $e->date }} • {{ $e->time }}
                </span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="users" class="h-3 w-3"></i>
                  {{ $e->registered_count ?? 0 }} registered
                </span>
              </div>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">No upcoming workshops.</div>
          @endforelse
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
          @forelse($departmentStats as $d)
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm">{{ $d['name'] }}</span>
                <span class="text-sm font-medium">{{ $d['employed'] }}%</span>
              </div>
              <div class="h-2 rounded-full bg-muted overflow-hidden">
                <div class="h-2 rounded-full bg-primary" style="width: {{ $d['employed'] }}%"></div>
              </div>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">No department data available yet.</div>
          @endforelse
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
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="{{ route('college.jobs.create') }}"
           class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
           data-testid="button-quick-job">
          <i data-lucide="briefcase" class="h-5 w-5"></i>
          <span class="text-sm">Post Job</span>
        </a>

        <a href="{{ route('college.workshops.create') }}"
           class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
           data-testid="button-quick-workshop">
          <i data-lucide="calendar-days" class="h-5 w-5"></i>
          <span class="text-sm">Add Workshop</span>
        </a>

        <a href="{{ route('college.announcements.create') }}"
           class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
           data-testid="button-quick-announcement">
          <i data-lucide="megaphone" class="h-5 w-5"></i>
          <span class="text-sm">New Announcement</span>
        </a>

        <a href="{{ route('college.scholarships.create') }}"
           class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
           data-testid="button-quick-scholarship">
          <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          <span class="text-sm">Add Scholarship</span>
        </a>

        <a href="{{ route('college.successStories.create') }}"
           class="rounded-md border border-border py-4 hover:bg-accent/50 transition flex flex-col items-center gap-2"
           data-testid="button-quick-story">
          <i data-lucide="award" class="h-5 w-5"></i>
          <span class="text-sm">Share Success Story</span>
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
