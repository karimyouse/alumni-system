@extends('layouts.dashboard')

@php
  $title='Workshops';
  $role='Alumni';
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
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Workshops</h1>
    <p class="text-sm text-muted-foreground">Upcoming workshops and events</p>
  </div>

  <div class="space-y-4">
    @foreach($workshops as $w)
      @php $isReg = in_array($w->id, $registeredIds); @endphp

      <div class="rounded-xl border border-border bg-card p-6 flex items-center justify-between">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            <i data-lucide="calendar-days" class="h-5 w-5"></i>
          </div>

          <div>
            <div class="flex items-center gap-2">
              <h3 class="font-semibold text-lg">{{ $w->title }}</h3>
              @if($isReg)
                <span class="text-xs rounded-full bg-green-500/15 text-green-400 px-2 py-1">Registered</span>
              @endif
            </div>

            <div class="mt-2 flex flex-wrap gap-3 text-xs text-muted-foreground">
              <span class="inline-flex items-center gap-1"><i data-lucide="calendar" class="h-3 w-3"></i> {{ $w->date }}</span>
              <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="h-3 w-3"></i> {{ $w->time }}</span>
              <span class="inline-flex items-center gap-1"><i data-lucide="map-pin" class="h-3 w-3"></i> {{ $w->location }}</span>
              <span class="inline-flex items-center gap-1"><i data-lucide="users" class="h-3 w-3"></i> {{ $w->spots }} spots left</span>
            </div>
          </div>
        </div>

        <div>
          @if($isReg)
            <form method="POST" action="{{ route('alumni.workshops.cancel', $w) }}">
              @csrf
              <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
                Cancel Registration
              </button>
            </form>
          @else
            <form method="POST" action="{{ route('alumni.workshops.register', $w) }}">
              @csrf
              <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
                      {{ $w->spots <= 0 ? 'disabled' : '' }}>
                Register
              </button>
            </form>
          @endif
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
