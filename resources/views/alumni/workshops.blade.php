@extends('layouts.dashboard')

@php
  $title = 'Workshops';
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

  $workshops = [
    ['title'=>'Career Development Workshop','date'=>'Jan 15, 2026','time'=>'10:00 AM - 2:00 PM','place'=>'Main Campus, Hall A','spots'=>'15 spots left','registered'=>true],
    ['title'=>'Technical Interview Prep','date'=>'Jan 20, 2026','time'=>'2:00 PM - 5:00 PM','place'=>'Online (Zoom)','spots'=>'30 spots left','registered'=>false],
    ['title'=>'Resume Writing Masterclass','date'=>'Jan 25, 2026','time'=>'11:00 AM - 1:00 PM','place'=>'Main Campus, Lab B','spots'=>'5 spots left','registered'=>false],
    ['title'=>'Networking Skills for Professionals','date'=>'Feb 1, 2026','time'=>'3:00 PM - 6:00 PM','place'=>'Community Center','spots'=>'25 spots left','registered'=>true],
    ['title'=>'Entrepreneurship Basics','date'=>'Feb 10, 2026','time'=>'9:00 AM - 12:00 PM','place'=>'Online (Teams)','spots'=>'50 spots left','registered'=>false],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Workshops</h1>
    <p class="text-sm text-muted-foreground">Upcoming workshops and events</p>
  </div>

  <div class="space-y-4">
    @foreach($workshops as $w)
      <div class="rounded-xl border border-border bg-card p-5">
        <div class="flex items-start justify-between gap-4">
          <div class="flex gap-4">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
              <i data-lucide="calendar-days" class="h-5 w-5"></i>
            </div>

            <div>
              <div class="flex items-center gap-2">
                <div class="font-semibold">{{ $w['title'] }}</div>
                @if($w['registered'])
                  <span class="inline-flex items-center rounded-full bg-green-500/15 px-2 py-0.5 text-xs text-green-400">
                    Registered
                  </span>
                @endif
              </div>

              <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground mt-2">
                <span class="inline-flex items-center gap-1"><i data-lucide="calendar" class="h-3 w-3"></i>{{ $w['date'] }}</span>
                <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="h-3 w-3"></i>{{ $w['time'] }}</span>
                <span class="inline-flex items-center gap-1"><i data-lucide="map-pin" class="h-3 w-3"></i>{{ $w['place'] }}</span>
                <span class="inline-flex items-center gap-1"><i data-lucide="users" class="h-3 w-3"></i>{{ $w['spots'] }}</span>
              </div>
            </div>
          </div>

          @if($w['registered'])
            <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
              Cancel Registration
            </button>
          @else
            <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
              Register
            </button>
          @endif
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
