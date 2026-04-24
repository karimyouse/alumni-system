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

  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold">My Job Postings</h1>
      <p class="text-sm text-muted-foreground">Manage your job postings</p>
    </div>

    <a href="{{ route('company.jobs.create') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Post New Job
    </a>
  </div>

  <div class="grid gap-4">
    @forelse($jobs as $job)
      <div class="rounded-xl border border-border bg-card">
        <div class="p-4 sm:p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
              <i data-lucide="briefcase-business" class="h-6 w-6"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-semibold leading-snug break-words">{{ $job->title }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $job->display_status_class }}">
                  {{ $job->display_status_label }}
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1 break-words">
                  <i data-lucide="map-pin" class="h-4 w-4"></i>
                  {{ $job->location ?: '—' }}
                </span>

                <span class="break-words">{{ $job->type ?: '—' }}</span>

                @if($job->display_salary !== '')
                  <span class="break-words">{{ $job->display_salary }}</span>
                @endif

                <span class="flex items-center gap-1">
                  <i data-lucide="clock-3" class="h-4 w-4"></i>
                  {{ $job->display_posted }}
                </span>

                <span class="flex items-center gap-1">
                  <i data-lucide="users" class="h-4 w-4"></i>
                  {{ $job->applications_count ?? 0 }} applicants
                </span>
              </div>
            </div>

            <div class="flex w-full items-center gap-2 flex-wrap justify-end md:w-auto md:justify-end">
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
