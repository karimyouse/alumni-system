@extends('layouts.dashboard')

@php
  $title = 'Scholarships';
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

  $scholarships = [
    ['title'=>'Graduate Excellence Award','desc'=>'GPA 3.5+ • Active participation','amount'=>'$5,000','deadline'=>'Feb 15, 2026','closing'=>false],
    ['title'=>'Tech Innovation Scholarship','desc'=>'Tech-related project submission','amount'=>'$3,000','deadline'=>'Mar 1, 2026','closing'=>false],
    ['title'=>'Community Leadership Grant','desc'=>'Community service record','amount'=>'$2,500','deadline'=>'Mar 15, 2026','closing'=>false],
    ['title'=>'Research Excellence Fund','desc'=>'Published research paper','amount'=>'$4,000','deadline'=>'Jan 30, 2026','closing'=>true],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Scholarships</h1>
    <p class="text-sm text-muted-foreground">Available scholarships and grants</p>
  </div>

  <div class="space-y-4">
    @foreach($scholarships as $s)
      <div class="rounded-xl border border-border bg-card p-5">
        <div class="flex items-start justify-between gap-4">
          <div class="flex gap-4">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
              <i data-lucide="graduation-cap" class="h-5 w-5"></i>
            </div>

            <div>
              <div class="flex items-center gap-2">
                <div class="font-semibold">{{ $s['title'] }}</div>
                @if($s['closing'])
                  <span class="inline-flex items-center rounded-full bg-red-500/15 px-2 py-0.5 text-xs text-red-400">
                    Closing Soon
                  </span>
                @endif
              </div>

              <div class="text-sm text-muted-foreground">{{ $s['desc'] }}</div>

              <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground mt-2">
                <span class="text-primary font-semibold">{{ $s['amount'] }}</span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="calendar" class="h-3 w-3"></i>
                  Deadline: {{ $s['deadline'] }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex gap-2">
            <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Details</button>
            <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">Apply</button>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
