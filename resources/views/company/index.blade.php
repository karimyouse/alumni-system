@extends('layouts.dashboard')

@php
  $title = __('Company Dashboard');
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>$applicationBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="rounded-xl border border-border bg-gradient-to-r from-primary/10 via-primary/5 to-transparent">
    <div class="p-6 flex items-start justify-between gap-4 flex-wrap">
      <div>
        <h1 class="text-2xl font-bold">Welcome, {{ $companyName }}!</h1>
        <p class="text-sm text-muted-foreground mt-1">
          Find qualified graduates and grow your team with top talent.
        </p>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
    <a href="{{ route('company.workshops.create') }}"
     class="inline-flex items-center gap-2 rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
    <i data-lucide="calendar-days" class="h-4 w-4"></i>
    Propose Workshop
    </a>

    <a href="{{ route('company.jobs.create') }}"
     class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
    <i data-lucide="plus" class="h-4 w-4"></i>
    Post New Job
  </a>
</div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Active Job Posts</div>
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="briefcase" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-4">{{ $activeJobsCount }}</div>
      <div class="text-sm text-muted-foreground mt-1">Currently hiring</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Applications</div>
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="file-text" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-4">{{ $applicationsCount }}</div>
      <div class="text-sm text-muted-foreground mt-1">Across all jobs</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Profile Views</div>
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="eye" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-4">{{ $profileViews }}</div>
      <div class="text-sm text-muted-foreground mt-1">From your job posts</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Candidates Viewed</div>
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="user-check" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-3xl font-bold mt-4">{{ $candidatesViewed }}</div>
      <div class="text-sm text-muted-foreground mt-1">Applicants reached</div>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <div class="xl:col-span-2 rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border flex items-center justify-between gap-3">
        <div>
          <div class="text-lg font-semibold">My Job Posts</div>
          <div class="text-sm text-muted-foreground">Manage your active and past job listings</div>
        </div>

        <a href="{{ route('company.jobs') }}"
           class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          Manage All <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-3">
        @forelse($latestJobs as $job)
          @php
            $statusClass = strtolower($job->display_status) === 'active'
              ? 'bg-green-500/10 text-green-400'
              : 'bg-secondary text-secondary-foreground';
          @endphp

          <div class="rounded-lg border border-border px-4 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <i data-lucide="briefcase" class="h-4 w-4"></i>
              </div>

              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <div class="font-semibold truncate">{{ $job->title }}</div>
                  <span class="text-[11px] rounded-full px-2 py-1 {{ $statusClass }}">
                    {{ $job->display_status }}
                  </span>
                </div>

                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                  <span>{{ $job->applications_count }} applications</span>
                  <span>{{ $job->display_views }} views</span>
                  <span>Posted {{ $job->display_posted }}</span>
                </div>
              </div>
            </div>

            <a href="{{ route('company.jobs.applicants', $job) }}"
               class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              View
            </a>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">No jobs posted yet.</div>
        @endforelse
      </div>
    </div>

    <div class="space-y-6">

      <div class="rounded-xl border border-border bg-card overflow-hidden">
        <div class="p-6 border-b border-border flex items-center justify-between">
          <div class="text-lg font-semibold">Recent Applications</div>
          <span class="text-xs rounded-full bg-secondary px-2 py-1 text-secondary-foreground">
            {{ $recentApplications->count() }} new
          </span>
        </div>

        <div class="p-6 space-y-4">
          @forelse($recentApplications as $application)
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-secondary flex items-center justify-center text-xs font-semibold flex-shrink-0">
                {{ $application->candidate_initials }}
              </div>

              <div class="min-w-0 flex-1">
                <div class="text-sm font-medium truncate">{{ $application->candidate_name }}</div>
                <div class="text-xs text-muted-foreground truncate">{{ $application->job_title }}</div>
              </div>

              <span class="text-[11px] rounded-full px-2 py-1 {{ $application->status_class }}">
                {{ $application->display_status }}
              </span>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">No recent applications yet.</div>
          @endforelse
        </div>

        <div class="px-6 pb-6">
          <a href="{{ route('company.applications') }}"
             class="block w-full text-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
            View All Applications
          </a>
        </div>
      </div>

      <div class="rounded-xl border border-border bg-card overflow-hidden">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold">Recommended Candidates</div>
        </div>

        <div class="p-6 space-y-4">
          @forelse($recommendedCandidates as $candidate)
            <div class="rounded-lg border border-border p-4">
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-9 h-9 rounded-full bg-secondary flex items-center justify-center text-xs font-semibold flex-shrink-0">
                    {{ $candidate['initials'] }}
                  </div>

                  <div class="min-w-0">
                    <div class="text-sm font-medium truncate">{{ $candidate['name'] }}</div>
                    <div class="text-xs text-muted-foreground">Class of {{ $candidate['graduation_year'] }}</div>
                  </div>
                </div>

                <span class="text-[11px] rounded-full border border-border px-2 py-1">
                  {{ $candidate['match'] }}% match
                </span>
              </div>

              <div class="mt-3 flex flex-wrap gap-2">
                @forelse($candidate['skills'] as $skill)
                  <span class="text-[11px] rounded-full bg-secondary px-2 py-1 text-secondary-foreground">
                    {{ $skill }}
                  </span>
                @empty
                  <span class="text-xs text-muted-foreground">No skills listed</span>
                @endforelse
              </div>
            </div>
          @empty
            <div class="text-sm text-muted-foreground">No candidate recommendations yet.</div>
          @endforelse
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
