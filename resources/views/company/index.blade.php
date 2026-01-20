@extends('layouts.dashboard')

@php
  $title='Company Dashboard';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Company Dashboard</h1>
      <p class="text-sm text-muted-foreground">Manage jobs and review applicants</p>
    </div>

    <a href="{{ route('company.jobs.create') }}"
       class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Post Job
    </a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Total Jobs</div>
      <div class="text-3xl font-bold mt-2">{{ $jobsCount }}</div>
    </div>
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Active Jobs</div>
      <div class="text-3xl font-bold mt-2">{{ $activeJobsCount }}</div>
    </div>
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Total Applications</div>
      <div class="text-3xl font-bold mt-2">{{ $applicationsCount }}</div>
    </div>
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="text-sm text-muted-foreground">Pending</div>
      <div class="text-3xl font-bold mt-2">{{ $pendingCount }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">Latest Jobs</div>
          <div class="text-sm text-muted-foreground">Your most recent job posts</div>
        </div>
        <a href="{{ route('company.jobs') }}" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
          View all <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-3">
        @forelse($latestJobs as $j)
          <div class="rounded-lg border border-border p-4 flex items-center justify-between">
            <div>
              <div class="font-semibold">{{ $j->title }}</div>
              <div class="text-xs text-muted-foreground">{{ $j->location ?? '-' }} • {{ $j->type ?? '-' }} • {{ ucfirst($j->status) }}</div>
            </div>
            <a href="{{ route('company.jobs.applicants', $j) }}"
               class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              Applicants
            </a>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">No jobs yet.</div>
        @endforelse
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-6 space-y-3">
      <div class="text-lg font-semibold">Application Status</div>
      <div class="text-sm text-muted-foreground">Current distribution</div>

      <div class="text-sm flex justify-between"><span>Reviewed</span><span class="font-semibold">{{ $reviewedCount }}</span></div>
      <div class="text-sm flex justify-between"><span>Accepted</span><span class="font-semibold">{{ $acceptedCount }}</span></div>
      <div class="text-sm flex justify-between"><span>Rejected</span><span class="font-semibold">{{ $rejectedCount }}</span></div>
    </div>
  </div>

</div>
@endsection
