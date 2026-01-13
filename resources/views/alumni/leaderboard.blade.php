@extends('layouts.dashboard')

@php
  $title = 'Leaderboard';
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

  $top = [
    ['rank'=>1,'initials'=>'AH','name'=>'Ahmed Hassan','points'=>1250,'delta'=>'+120 this month','highlight'=>'gold'],
    ['rank'=>2,'initials'=>'SA','name'=>'Sara Ali','points'=>1180,'delta'=>'+95 this month','highlight'=>'silver'],
    ['rank'=>3,'initials'=>'OK','name'=>'Omar Khalil','points'=>1050,'delta'=>'+80 this month','highlight'=>'bronze'],
  ];

  $full = [
    ['rank'=>1,'initials'=>'AH','name'=>'Ahmed Hassan','activities'=>45,'points'=>1250,'delta'=>'+120'],
    ['rank'=>2,'initials'=>'SA','name'=>'Sara Ali','activities'=>42,'points'=>1180,'delta'=>'+95'],
    ['rank'=>3,'initials'=>'OK','name'=>'Omar Khalil','activities'=>38,'points'=>1050,'delta'=>'+80'],
    ['rank'=>4,'initials'=>'LH','name'=>'Layla Hassan','activities'=>35,'points'=>980,'delta'=>'+65'],
    ['rank'=>5,'initials'=>'MN','name'=>'Mohammed Nasser','activities'=>33,'points'=>920,'delta'=>'+55'],
    ['rank'=>6,'initials'=>'FY','name'=>'Fatima Yousef','activities'=>30,'points'=>870,'delta'=>'+50'],
    ['rank'=>7,'initials'=>'KI','name'=>'Khaled Ibrahim','activities'=>28,'points'=>850,'delta'=>'+45'],
    ['rank'=>8,'initials'=>'NA','name'=>'Nour Ahmad','activities'=>27,'points'=>820,'delta'=>'+40'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Leaderboard</h1>
    <p class="text-sm text-muted-foreground">Monthly rankings based on engagement and activities</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @foreach($top as $t)
      @php
        $border = $t['rank'] === 1 ? 'border-yellow-500/40' : ($t['rank'] === 2 ? 'border-muted' : 'border-muted');
      @endphp

      <div class="rounded-xl border {{ $t['rank']==1 ? 'border-yellow-500/40' : 'border-border' }} bg-card p-6 text-center relative">
        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
          <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-background border border-border text-xs font-semibold">
            {{ $t['rank'] }}
          </span>
        </div>

        @if($t['rank'] === 1)
          <div class="text-yellow-400 mb-3">
            <i data-lucide="trophy" class="h-5 w-5 mx-auto"></i>
          </div>
        @endif

        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto text-xs font-semibold">
          {{ $t['initials'] }}
        </div>

        <div class="mt-3 font-semibold">{{ $t['name'] }}</div>
        <div class="text-primary font-bold mt-1">{{ $t['points'] }}</div>
        <div class="text-xs text-muted-foreground">points</div>

        <div class="mt-3 inline-flex items-center rounded-full bg-secondary px-3 py-1 text-xs">
          {{ $t['delta'] }}
        </div>
      </div>
    @endforeach
  </div>

  <div class="rounded-xl border border-border bg-card">
    <div class="p-6 border-b border-border flex items-center gap-2">
      <i data-lucide="trophy" class="h-4 w-4"></i>
      <h2 class="font-semibold">Full Rankings</h2>
    </div>

    <div class="p-6 space-y-3">
      @foreach($full as $r)
        <div class="flex items-center justify-between rounded-lg bg-accent/40 p-4">
          <div class="flex items-center gap-3">
            <div class="w-6 text-xs text-muted-foreground font-semibold">{{ $r['rank'] }}</div>
            <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
              {{ $r['initials'] }}
            </div>
            <div>
              <div class="text-sm font-semibold">{{ $r['name'] }}</div>
              <div class="text-xs text-muted-foreground">{{ $r['activities'] }} activities</div>
            </div>
          </div>

          <div class="text-right">
            <div class="text-sm font-semibold text-primary">{{ $r['points'] }}</div>
            <div class="text-xs text-green-500">{{ $r['delta'] }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
