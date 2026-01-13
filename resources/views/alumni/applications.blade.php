@extends('layouts.dashboard')

@php
  $title = 'My Applications';
  $role  = 'Alumni';
  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>12],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days','badge'=>3],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];

  $tabs = [
    ['key'=>'all','label'=>'All','count'=>6],
    ['key'=>'jobs','label'=>'Jobs','count'=>4],
    ['key'=>'scholarships','label'=>'Scholarships','count'=>1],
    ['key'=>'workshops','label'=>'Workshops','count'=>1],
  ];

  $items = [
    ['title'=>'Frontend Developer','org'=>'TechCorp','date'=>'Applied Dec 20, 2025','status'=>'Pending','statusColor'=>'bg-muted'],
    ['title'=>'Software Engineer','org'=>'StartupX','date'=>'Applied Dec 18, 2025','status'=>'Under Review','statusColor'=>'bg-blue-500/15 text-blue-400'],
    ['title'=>'UI Designer','org'=>'DesignHub','date'=>'Applied Dec 10, 2025','status'=>'Accepted','statusColor'=>'bg-green-500/15 text-green-400'],
    ['title'=>'Backend Developer','org'=>'DataCo','date'=>'Applied Dec 5, 2025','status'=>'Rejected','statusColor'=>'bg-red-500/15 text-red-400'],
    ['title'=>'Tech Innovation Scholarship','org'=>'PTC','date'=>'Applied Dec 22, 2025','status'=>'Pending','statusColor'=>'bg-muted'],
    ['title'=>'Career Development Workshop','org'=>'PTC','date'=>'Applied Dec 15, 2025','status'=>'Accepted','statusColor'=>'bg-green-500/15 text-green-400'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">My Applications</h1>
    <p class="text-sm text-muted-foreground">Track your job, scholarship, and workshop applications</p>
  </div>

  
  <div class="inline-flex rounded-lg bg-muted p-1 gap-1">
    @foreach($tabs as $i => $t)
      <button
        type="button"
        class="px-3 py-2 text-sm rounded-md {{ $i==0 ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground' }}">
        {{ $t['label'] }} ({{ $t['count'] }})
      </button>
    @endforeach
  </div>

  <div class="space-y-3">
    @foreach($items as $it)
      <div class="rounded-xl border border-border bg-card p-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <i data-lucide="briefcase" class="h-5 w-5"></i>
          </div>

          <div>
            <div class="flex items-center gap-2">
              <div class="font-semibold">{{ $it['title'] }}</div>
              <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs {{ $it['statusColor'] }}">
                {{ $it['status'] }}
              </span>
            </div>
            <div class="text-xs text-muted-foreground mt-1">
              {{ $it['org'] }} &nbsp;•&nbsp; {{ $it['date'] }}
            </div>
          </div>
        </div>

        <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          View Details
        </button>
      </div>
    @endforeach
  </div>
</div>
@endsection
