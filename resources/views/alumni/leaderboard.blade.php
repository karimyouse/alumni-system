@extends('layouts.dashboard')

@php
  $title = __('Leaderboard');
  $role='Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square','badge'=>$recommendationsReceived ?? 0],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text','badge'=>$applicationsBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Leaderboard</h1>
    <p class="text-sm text-muted-foreground">Monthly rankings based on engagement and activities</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @forelse($topThree as $item)
      <div class="rounded-xl border {{ $item['rank'] === 1 ? 'border-yellow-500/40' : 'border-border' }} bg-card p-4 text-center relative sm:p-6">
        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
          <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-background border border-border text-xs font-semibold">
            {{ $item['rank'] }}
          </span>
        </div>

        @if($item['rank'] === 1)
          <div class="text-yellow-400 mb-3">
            <i data-lucide="trophy" class="h-5 w-5 mx-auto"></i>
          </div>
        @endif

        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto text-xs font-semibold">
          {{ $item['initials'] }}
        </div>

        <div class="mt-3 font-semibold break-words">
          {{ $item['name'] }}
          @if($item['is_me'])
            <span class="text-xs text-primary">(You)</span>
          @endif
        </div>

        <div class="text-primary font-bold mt-1">{{ number_format($item['points']) }}</div>
        <div class="text-xs text-muted-foreground">points</div>

        <div class="mt-3 inline-flex items-center rounded-full bg-secondary px-3 py-1 text-xs">
          {{ $item['activities'] }} activities
        </div>
      </div>
    @empty
      <div class="lg:col-span-3 rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No leaderboard data yet.
      </div>
    @endforelse
  </div>

  <div class="rounded-xl border border-border bg-card">
    <div class="p-4 border-b border-border flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between sm:p-6">
      <div class="flex items-center gap-2">
        <i data-lucide="trophy" class="h-4 w-4"></i>
        <h2 class="font-semibold">Full Rankings</h2>
      </div>

      @if($myRank)
        <span class="text-xs rounded-full bg-primary/10 text-primary px-3 py-1">
          Your rank: #{{ $myRank }}
        </span>
      @endif
    </div>

    <div class="p-6 space-y-3">
      @forelse($ranked as $item)
        <div class="flex flex-col gap-3 rounded-lg p-4 sm:flex-row sm:items-center sm:justify-between {{ $item['is_me'] ? 'bg-primary/10 border border-primary/20' : 'bg-accent/40' }}">
          <div class="flex items-center gap-3 min-w-0">
            <div class="w-6 text-xs text-muted-foreground font-semibold">{{ $item['rank'] }}</div>

            <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold flex-shrink-0">
              {{ $item['initials'] }}
            </div>

            <div class="min-w-0">
              <div class="text-sm font-semibold truncate">
                {{ $item['name'] }}
                @if($item['is_me'])
                  <span class="text-xs text-primary">(You)</span>
                @endif
              </div>

              <div class="text-xs text-muted-foreground break-words">
                {{ $item['activities'] }} activities •
                {{ $item['applications_count'] }} applications •
                {{ $item['workshops_count'] }} workshops •
                {{ $item['received_recommendations'] }} received recommendations
              </div>
            </div>
          </div>

          <div class="text-left sm:text-right">
            <div class="text-sm font-semibold text-primary">{{ number_format($item['points']) }}</div>
            <div class="text-xs text-muted-foreground">points</div>
          </div>
        </div>
      @empty
        <div class="text-sm text-muted-foreground">No leaderboard entries yet.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
