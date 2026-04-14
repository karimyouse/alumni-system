@extends('layouts.dashboard')

@php
  $title = __('Workshops');
  $role='Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days'],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square','badge'=>$recommendationsReceived ?? 0],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text','badge'=>$applicationsBadgeCount ?? 0],
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
      @php
        $isReg = in_array($w->id, $registeredIds);


        $cap = $w->capacity ?? null;



        $registeredCount = $w->registered_count ?? 0;


        $spotsLeft = is_null($cap) ? null : max(0, (int)$cap - (int)$registeredCount);


        $isFull = (!is_null($cap) && $spotsLeft <= 0);


        $canRegister = (!$isReg && !$isFull);
      @endphp

      <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex min-w-0 items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
            <i data-lucide="calendar-days" class="h-5 w-5"></i>
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <h3 class="font-semibold text-lg leading-snug break-words">{{ $w->title }}</h3>

              @if($isReg)
                <span class="text-xs rounded-full bg-green-500/15 text-green-400 px-2 py-1">Registered</span>
              @elseif($isFull)
                <span class="text-xs rounded-full bg-red-500/15 text-red-400 px-2 py-1">Full</span>
              @endif
            </div>

            <div class="mt-2 flex flex-wrap gap-3 text-xs text-muted-foreground">
              <span class="inline-flex items-center gap-1">
                <i data-lucide="calendar" class="h-3 w-3"></i> {{ $w->date }}
              </span>

              <span class="inline-flex items-center gap-1">
                <i data-lucide="clock" class="h-3 w-3"></i> {{ $w->time }}
              </span>

              <span class="inline-flex items-center gap-1 break-words">
                <i data-lucide="map-pin" class="h-3 w-3"></i> {{ $w->location }}
              </span>

              <span class="inline-flex items-center gap-1">
                <i data-lucide="users" class="h-3 w-3"></i>
                @if(is_null($cap))
                  Unlimited spots
                @else
                  {{ $spotsLeft }} spots left
                @endif
              </span>
            </div>
          </div>
        </div>

        <div class="w-full sm:w-auto sm:flex-shrink-0">
          @if($isReg)
            <form method="POST" action="{{ route('alumni.workshops.cancel', $w) }}">
              @csrf
              <button class="w-full rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
                Cancel Registration
              </button>
            </form>
          @else
            <form method="POST" action="{{ route('alumni.workshops.register', $w) }}">
              @csrf
              <button class="w-full rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
                      {{ $canRegister ? '' : 'disabled' }}>
                Register
              </button>
            </form>
          @endif
        </div>
        </div>

        @if(($w->company || $w->company_user_id) && (($w->organizer_role ?? null) === 'company' || $w->company_user_id))
          @include('partials.company-trust-card', [
            'company' => $w->company,
            'fallbackName' => $w->company?->name,
            'variant' => 'embedded',
          ])
        @endif
      </div>
    @endforeach
  </div>
</div>
@endsection
