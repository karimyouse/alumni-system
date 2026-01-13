@extends('layouts.dashboard')

@php
  $title = 'Recommendations';
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

  $received = [
    ['initials'=>'SA','name'=>'Sara Ali','role'=>'Senior Developer at TechCorp','date'=>'Dec 15, 2025','text'=>'Ahmed is an exceptional developer with great problem-solving skills. Highly recommended!'],
    ['initials'=>'OK','name'=>'Omar Khalil','role'=>'Project Manager at StartupX','date'=>'Nov 28, 2025','text'=>'Worked with Ahmed on multiple projects. Very professional and reliable team member.'],
  ];

  $given = [
    ['initials'=>'LH','name'=>'Layla Hassan','role'=>'UI/UX Designer','date'=>'Dec 10, 2025','text'=>'Layla has an amazing eye for design and user experience. A pleasure to work with!'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Recommendations</h1>
    <p class="text-sm text-muted-foreground">Give and receive peer recommendations</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center gap-2">
        <i data-lucide="star" class="h-4 w-4 text-yellow-400"></i>
        <h2 class="font-semibold">Received Recommendations</h2>
      </div>

      <div class="p-6 space-y-4">
        @foreach($received as $r)
          <div class="rounded-lg border border-border bg-background/40 p-4">
            <div class="flex items-start justify-between gap-3">
              <div class="flex gap-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                  {{ $r['initials'] }}
                </div>
                <div>
                  <div class="font-semibold text-sm">{{ $r['name'] }}</div>
                  <div class="text-xs text-muted-foreground">{{ $r['role'] }}</div>
                </div>
              </div>
              <div class="text-xs text-muted-foreground">{{ $r['date'] }}</div>
            </div>

            <div class="mt-3 text-sm text-muted-foreground">
              {{ $r['text'] }}
            </div>
          </div>
        @endforeach
      </div>
    </div>

    
    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center gap-2">
        <i data-lucide="send" class="h-4 w-4 text-primary"></i>
        <h2 class="font-semibold">Given Recommendations</h2>
      </div>

      <div class="p-6 space-y-4">
        @foreach($given as $g)
          <div class="rounded-lg border border-border bg-background/40 p-4">
            <div class="flex items-start justify-between gap-3">
              <div class="flex gap-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                  {{ $g['initials'] }}
                </div>
                <div>
                  <div class="font-semibold text-sm">{{ $g['name'] }}</div>
                  <div class="text-xs text-muted-foreground">{{ $g['role'] }}</div>
                </div>
              </div>
              <div class="text-xs text-muted-foreground">{{ $g['date'] }}</div>
            </div>

            <div class="mt-3 text-sm text-muted-foreground">
              {{ $g['text'] }}
            </div>
          </div>
        @endforeach

        <div class="border-t border-border pt-4">
          <div class="text-sm font-medium mb-2">Write a new recommendation</div>
          <textarea class="w-full min-h-28 rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    placeholder="Search for a peer and write your recommendation..."></textarea>
          <button class="mt-3 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2">
            <i data-lucide="send" class="h-4 w-4"></i>
            Send Recommendation
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
