@extends('layouts.dashboard')

@php
  $title = 'Company Dashboard';
  $role  = 'Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>8],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];

  $userName = auth()->user()->name ?? 'Company';

  $myJobs = [
    ['id'=>'1','title'=>'Frontend Developer','applications'=>12,'views'=>156,'status'=>'Active','posted'=>'Jan 5, 2025'],
    ['id'=>'2','title'=>'Backend Engineer','applications'=>8,'views'=>98,'status'=>'Active','posted'=>'Jan 8, 2025'],
    ['id'=>'3','title'=>'UI/UX Designer','applications'=>5,'views'=>67,'status'=>'Closed','posted'=>'Dec 20, 2024'],
  ];

  $recentApplications = [
    ['id'=>'1','name'=>'Ahmed Hassan','job'=>'Frontend Developer','date'=>'2 hours ago','status'=>'Pending'],
    ['id'=>'2','name'=>'Sara Ali','job'=>'Backend Engineer','date'=>'5 hours ago','status'=>'Pending'],
    ['id'=>'3','name'=>'Omar Khalil','job'=>'Frontend Developer','date'=>'1 day ago','status'=>'Reviewed'],
    ['id'=>'4','name'=>'Mona Ibrahim','job'=>'UI/UX Designer','date'=>'2 days ago','status'=>'Rejected'],
  ];

  $topCandidates = [
    ['id'=>'1','name'=>'Ahmed Hassan','skills'=>['React','TypeScript','Node.js'],'year'=>2024,'match'=>95],
    ['id'=>'2','name'=>'Sara Ali','skills'=>['Python','Django','PostgreSQL'],'year'=>2023,'match'=>88],
    ['id'=>'3','name'=>'Layla Mahmoud','skills'=>['React','Vue.js','CSS'],'year'=>2024,'match'=>82],
  ];

  $statusIcon = fn($s) => match($s) {
    'Pending'  => 'alert-circle',
    'Reviewed' => 'check-circle',
    'Rejected' => 'x-circle',
    default => null,
  };
  $statusColor = fn($s) => match($s) {
    'Pending'  => 'text-yellow-500',
    'Reviewed' => 'text-blue-500',
    'Rejected' => 'text-red-500',
    default => 'text-muted-foreground',
  };
@endphp

@section('content')
<div class="space-y-6">


  <div class="rounded-xl border border-purple-500/20 bg-gradient-to-r from-purple-500/10 via-purple-500/5 to-transparent">
    <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold mb-1">Welcome, {{ $userName }}!</h2>
        <p class="text-muted-foreground">Find qualified graduates and grow your team with top talent.</p>
      </div>

      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
              data-testid="button-post-new-job">
        <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
        Post New Job
      </button>
    </div>
  </div>


  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Active Job Posts</div>
        <i data-lucide="briefcase" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">2</div>
      <div class="text-xs text-muted-foreground mt-1">Currently hiring</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Applications</div>
        <i data-lucide="file-text" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">25</div>
      <div class="text-xs text-muted-foreground mt-1">Across all jobs</div>
      <div class="text-xs text-green-500 mt-1">▲ 15%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Profile Views</div>
        <i data-lucide="eye" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">321</div>
      <div class="text-xs text-muted-foreground mt-1">This month</div>
      <div class="text-xs text-green-500 mt-1">▲ 8%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Candidates Viewed</div>
        <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">48</div>
      <div class="text-xs text-muted-foreground mt-1">Alumni profiles browsed</div>
    </div>
  </div>


  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between gap-2">
        <div>
          <div class="text-lg font-semibold">My Job Posts</div>
          <div class="text-sm text-muted-foreground">Manage your active and past job listings</div>
        </div>

        <a href="/company/jobs" class="text-sm text-primary hover:underline inline-flex items-center gap-1"
           data-testid="button-manage-jobs">
          Manage All <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
      </div>

      <div class="p-6 space-y-4">
        @foreach($myJobs as $job)
          <div class="flex items-center gap-4 p-4 rounded-lg border border-border hover:bg-accent/30 transition"
               data-testid="card-job-{{ $job['id'] }}">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="briefcase" class="h-5 w-5 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h4 class="font-medium">{{ $job['title'] }}</h4>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                  {{ $job['status']==='Active' ? 'bg-green-500/10 text-green-400' : 'bg-secondary text-secondary-foreground' }}">
                  {{ $job['status'] }}
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                <span class="flex items-center gap-1">
                  <i data-lucide="file-text" class="h-3 w-3"></i>
                  {{ $job['applications'] }} applications
                </span>
                <span class="flex items-center gap-1">
                  <i data-lucide="eye" class="h-3 w-3"></i>
                  {{ $job['views'] }} views
                </span>
                <span class="flex items-center gap-1">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  Posted {{ $job['posted'] }}
                </span>
              </div>
            </div>

            <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              View
            </button>
          </div>
        @endforeach
      </div>
    </div>


    <div class="space-y-6">


      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border flex items-center justify-between">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="file-text" class="h-4 w-4"></i>
            Recent Applications
          </div>
          <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">8 new</span>
        </div>

        <div class="p-6 space-y-3">
          @foreach($recentApplications as $app)
            @php $initials = collect(explode(' ', $app['name']))->map(fn($n)=>mb_substr($n,0,1))->join(''); @endphp
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-accent/50 transition">
              <div class="h-8 w-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                {{ $initials }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ $app['name'] }}</p>
                <p class="text-xs text-muted-foreground truncate">{{ $app['job'] }}</p>
              </div>

              @php $ic = $statusIcon($app['status']); @endphp
              @if($ic)
                <i data-lucide="{{ $ic }}" class="h-4 w-4 {{ $statusColor($app['status']) }}"></i>
              @endif
            </div>
          @endforeach

          <a href="/company/applications" class="block">
            <button class="w-full rounded-md px-3 py-2 text-sm hover:bg-accent/50 transition">
              View All Applications
            </button>
          </a>
        </div>
      </div>

      
      <div class="rounded-xl border border-border bg-card">
        <div class="p-6 border-b border-border">
          <div class="text-lg font-semibold inline-flex items-center gap-2">
            <i data-lucide="users" class="h-4 w-4"></i>
            Recommended Candidates
          </div>
        </div>

        <div class="p-6 space-y-4">
          @foreach($topCandidates as $c)
            @php $ini = collect(explode(' ', $c['name']))->map(fn($n)=>mb_substr($n,0,1))->join(''); @endphp
            <div class="p-3 rounded-lg border border-border">
              <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2">
                  <div class="h-8 w-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                    {{ $ini }}
                  </div>
                  <div>
                    <p class="text-sm font-medium">{{ $c['name'] }}</p>
                    <p class="text-xs text-muted-foreground inline-flex items-center gap-1">
                      <i data-lucide="graduation-cap" class="h-3 w-3"></i>
                      Class of {{ $c['year'] }}
                    </p>
                  </div>
                </div>

                <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">
                  {{ $c['match'] }}% match
                </span>
              </div>

              <div class="flex flex-wrap gap-1">
                @foreach(array_slice($c['skills'],0,3) as $skill)
                  <span class="inline-flex items-center rounded-full border border-border px-3 py-1 text-xs">
                    {{ $skill }}
                  </span>
                @endforeach
              </div>
            </div>
          @endforeach

          <a href="/company/alumni" class="block">
            <button class="w-full rounded-md px-3 py-2 text-sm hover:bg-accent/50 transition">
              Browse All Candidates
            </button>
          </a>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
