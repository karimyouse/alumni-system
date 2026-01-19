@extends('layouts.dashboard')

@php
  $title = 'Scholarship Details';
  $role  = 'Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase'],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days'],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];

  $pill = fn($s) => match($s) {
    'open' => ['Open','bg-green-500/15 text-green-400'],
    'closing_soon' => ['Closing Soon','bg-orange-500/15 text-orange-400'],
    'closed' => ['Closed','bg-red-500/15 text-red-400'],
    default => [ucfirst($s),'bg-secondary text-secondary-foreground'],
  };
  [$pillTxt,$pillCls] = $pill($scholarship->status ?? 'open');
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold">{{ $scholarship->title }}</h1>
        <span class="text-xs rounded-full px-2 py-1 {{ $pillCls }}">{{ $pillTxt }}</span>
        @if($alreadyApplied)
          <span class="text-xs rounded-full px-2 py-1 bg-blue-500/15 text-blue-400">Applied</span>
        @endif
      </div>
      <p class="text-sm text-muted-foreground mt-1">Review details then apply</p>
    </div>

    <a href="{{ route('alumni.scholarships') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-6 space-y-4">
    <div class="grid md:grid-cols-2 gap-3 text-sm">
      <div><span class="text-muted-foreground">Amount:</span> {{ $scholarship->amount ?? '-' }}</div>
      <div><span class="text-muted-foreground">Deadline:</span> {{ $scholarship->deadline ?? '-' }}</div>
    </div>

    @if($scholarship->description)
      <div>
        <div class="font-semibold mb-1">Description</div>
        <div class="text-sm text-muted-foreground">{{ $scholarship->description }}</div>
      </div>
    @endif

    @if($scholarship->requirements)
      <div>
        <div class="font-semibold mb-1">Requirements</div>
        <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $scholarship->requirements }}</div>
      </div>
    @endif

    <div class="pt-2 flex items-center gap-2">
      @if(!$alreadyApplied && ($scholarship->status ?? 'open') !== 'closed')
        <form method="POST" action="{{ route('alumni.scholarships.apply', $scholarship) }}">
          @csrf
          <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
            Apply Now
          </button>
        </form>
      @else
        <button class="rounded-md border border-border px-4 py-2 text-sm opacity-70 cursor-not-allowed" disabled>
          {{ $alreadyApplied ? 'Already Applied' : 'Closed' }}
        </button>
      @endif
    </div>
  </div>

</div>
@endsection
