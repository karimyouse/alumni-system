@extends('layouts.dashboard')

@php
  $title = 'Jobs';
  $role  = 'Company';
  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];

  $jobs = [
    ['id'=>'1','title'=>'Frontend Developer','location'=>'Gaza','type'=>'Full-time','salary'=>'$800-$1200','status'=>'active','applicants'=>15,'posted'=>'Dec 20, 2025'],
    ['id'=>'2','title'=>'Backend Engineer','location'=>'Remote','type'=>'Full-time','salary'=>'$1000-$1500','status'=>'active','applicants'=>22,'posted'=>'Dec 18, 2025'],
    ['id'=>'3','title'=>'UI/UX Designer','location'=>'Ramallah','type'=>'Part-time','salary'=>'$500-$800','status'=>'pending','applicants'=>0,'posted'=>'Dec 22, 2025'],
    ['id'=>'4','title'=>'Data Analyst','location'=>'Gaza','type'=>'Full-time','salary'=>'$700-$1100','status'=>'closed','applicants'=>18,'posted'=>'Nov 15, 2025'],
  ];

  $badge = fn($s) => match($s) {
    'active'  => ['Active','bg-green-600 text-white'],
    'pending' => ['Pending Approval','bg-secondary text-secondary-foreground'],
    'closed'  => ['Closed','border border-border text-muted-foreground'],
    default   => [$s,'bg-secondary text-secondary-foreground'],
  };
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Jobs</h1>
      <p class="text-muted-foreground">Manage your job postings</p>
    </div>

    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-post-new-job">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Post New Job
    </button>
  </div>

  <div class="grid gap-4">
    @foreach($jobs as $j)
      @php [$txt,$cls] = $badge($j['status']); @endphp
      <div class="rounded-xl border border-border bg-card" data-testid="card-job-{{ $j['id'] }}">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="briefcase" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $j['title'] }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $cls }}">{{ $txt }}</span>
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1"><i data-lucide="map-pin" class="h-4 w-4"></i>{{ $j['location'] }}</span>
                <span>{{ $j['type'] }}</span>
                <span>{{ $j['salary'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="clock" class="h-4 w-4"></i>Posted {{ $j['posted'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="users" class="h-4 w-4"></i>{{ $j['applicants'] }} applicants</span>
              </div>
            </div>

            <div class="flex gap-2">
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-view-{{ $j['id'] }}">
                <i data-lucide="eye" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-edit-{{ $j['id'] }}">
                <i data-lucide="edit" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-delete-{{ $j['id'] }}">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
