@extends('layouts.dashboard')

@php
  $title = 'Jobs';
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

  $jobs = [
    ['id'=>'1','title'=>'Frontend Developer','company'=>'TechCorp','location'=>'Gaza','type'=>'Full-time','status'=>'pending','posted'=>'Dec 20, 2025','applicants'=>12],
    ['id'=>'2','title'=>'Software Engineer','company'=>'StartupX','location'=>'Remote','type'=>'Full-time','status'=>'approved','posted'=>'Dec 18, 2025','applicants'=>25],
    ['id'=>'3','title'=>'UI/UX Designer','company'=>'DesignHub','location'=>'Ramallah','type'=>'Part-time','status'=>'approved','posted'=>'Dec 15, 2025','applicants'=>8],
    ['id'=>'4','title'=>'Data Analyst','company'=>'DataCo','location'=>'Gaza','type'=>'Full-time','status'=>'rejected','posted'=>'Dec 10, 2025','applicants'=>0],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Jobs</h1>
    <p class="text-muted-foreground">Review and manage job postings from companies</p>
  </div>

  <div class="grid gap-4">
    @foreach($jobs as $j)
      <div class="rounded-xl border border-border bg-card" data-testid="card-job-{{ $j['id'] }}">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="building-2" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $j['title'] }}</h3>

                @if($j['status']==='pending')
                  <span class="inline-flex items-center rounded-full bg-secondary px-2 py-1 text-xs">
                    <i data-lucide="clock" class="h-3 w-3 mr-1"></i> Pending Review
                  </span>
                @elseif($j['status']==='approved')
                  <span class="inline-flex items-center rounded-full bg-green-600/20 text-green-400 px-2 py-1 text-xs">
                    <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i> Approved
                  </span>
                @else
                  <span class="inline-flex items-center rounded-full bg-red-500/15 text-red-400 px-2 py-1 text-xs">
                    <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i> Rejected
                  </span>
                @endif
              </div>

              <p class="text-muted-foreground">{{ $j['company'] }}</p>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1"><i data-lucide="map-pin" class="h-4 w-4"></i>{{ $j['location'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="briefcase" class="h-4 w-4"></i>{{ $j['type'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="calendar" class="h-4 w-4"></i>Posted {{ $j['posted'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="users" class="h-4 w-4"></i>{{ $j['applicants'] }} applicants</span>
              </div>
            </div>

            <div class="flex gap-2">
              @if($j['status']==='pending')
                <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50"
                        data-testid="button-reject-{{ $j['id'] }}">Reject</button>
                <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
                        data-testid="button-approve-{{ $j['id'] }}">Approve</button>
              @else
                <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50"
                        data-testid="button-view-{{ $j['id'] }}">View Details</button>
              @endif
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
