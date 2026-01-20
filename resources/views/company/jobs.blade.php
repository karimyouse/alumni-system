@extends('layouts.dashboard')

@php
  $title='Company Jobs';
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
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Jobs</h1>
      <p class="text-sm text-muted-foreground">Manage your job postings and applicants</p>
    </div>

    <a href="{{ route('company.jobs.create') }}"
       class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Post Job
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Title</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Applicants</th>
            <th class="text-left p-4 font-medium">Posted</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jobs as $job)
            @php
              $pill = match($job->status){
                'active' => ['Active','bg-green-500/15 text-green-400'],
                'closed' => ['Closed','bg-red-500/15 text-red-400'],
                default => [ucfirst($job->status),'bg-secondary text-secondary-foreground'],
              };
            @endphp
            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $job->title }}</div>
                <div class="text-xs text-muted-foreground">{{ $job->location ?? '-' }} • {{ $job->type ?? '-' }}</div>
              </td>

              <td class="p-4">
                <span class="text-xs rounded-full px-2 py-1 {{ $pill[1] }}">{{ $pill[0] }}</span>
              </td>

              <td class="p-4">{{ $job->applications_count ?? 0 }}</td>

              <td class="p-4 text-sm text-muted-foreground">{{ $job->posted ?? '-' }}</td>

              <td class="p-4">
                <a href="{{ route('company.jobs.applicants', $job) }}"
                   class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50 inline-flex items-center gap-2">
                  <i data-lucide="users" class="h-4 w-4"></i>
                  Applicants
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="5">
                No jobs posted yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    {{ $jobs->links() }}
  </div>
</div>
@endsection
