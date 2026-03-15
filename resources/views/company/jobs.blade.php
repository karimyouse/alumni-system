@extends('layouts.dashboard')

@php
  $title = __('My Job Postings');
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

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">My Job Postings</h1>
      <p class="text-sm text-muted-foreground">Manage your job postings</p>
    </div>

    <a href="{{ route('company.jobs.create') }}"
       class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Post New Job
    </a>
  </div>

  <div class="space-y-4">
    @forelse($jobs as $job)
      <div class="rounded-xl border border-border bg-card px-5 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap lg:flex-nowrap">

          <div class="flex items-center gap-4 min-w-0 flex-1">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
              <i data-lucide="briefcase-business" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="font-semibold text-base md:text-lg leading-tight tracking-tight">
                  {{ $job->title }}
                </h3>

                <span class="text-[11px] rounded-full px-3 py-1 {{ $job->display_status_class }}">
                  {{ $job->display_status_label }}
                </span>
              </div>

              <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                  {{ $job->location ?: '—' }}
                </span>

                <span>{{ $job->type ?: '—' }}</span>

                @if($job->display_salary !== '')
                  <span>{{ $job->display_salary }}</span>
                @endif

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="clock-3" class="h-3.5 w-3.5"></i>
                  {{ $job->display_posted }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="users" class="h-3.5 w-3.5"></i>
                  {{ $job->applications_count ?? 0 }} applicants
                </span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 flex-wrap justify-end">
            <a href="{{ route('company.jobs.applicants', $job) }}"
               class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
               title="Applicants">
              <i data-lucide="eye" class="h-4 w-4"></i>
            </a>

            <a href="{{ route('company.jobs.edit', $job) }}"
               class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
               title="Edit">
              <i data-lucide="file-pen-line" class="h-4 w-4"></i>
            </a>

            <form method="POST" action="{{ route('company.jobs.delete', $job) }}"
                  onsubmit="return confirm('Delete this job?');">
              @csrf
              <button type="submit"
                      class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                      title="Delete">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
            </form>
          </div>

        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No jobs posted yet.
      </div>
    @endforelse
  </div>

  <div>
    {{ $jobs->links() }}
  </div>

</div>
@endsection
