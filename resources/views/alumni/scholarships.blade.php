@extends('layouts.dashboard')

@php
  $title = __('Scholarships');
  $role  = 'Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard', 'badge'=>null],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user', 'badge'=>null],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase', 'badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days', 'badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap', 'badge'=>null],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square', 'badge'=>$recommendationsReceived ?? 0],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy', 'badge'=>null],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text', 'badge'=>$applicationsBadgeCount ?? 0],
  ];

  $statusPill = function ($status) {
    return match (strtolower((string) $status)) {
      'open' => ['Open', 'bg-green-500/15 text-green-400'],
      'closed' => ['Closed', 'bg-red-500/15 text-red-400'],
      default => [ucfirst((string) $status), 'bg-secondary text-secondary-foreground'],
    };
  };
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold">Scholarships</h1>
      <p class="text-sm text-muted-foreground sm:text-base">Explore available scholarships</p>
    </div>
  </div>

  <div class="space-y-4">
    @forelse($scholarships as $s)
      @php
        [$pillTxt, $pillCls] = $statusPill($s->status ?? 'open');
        $applied = in_array($s->id, $appliedIds ?? []);
      @endphp

      <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex min-w-0 items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
            <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="font-semibold text-lg leading-snug break-words">{{ $s->title }}</h3>
              <span class="text-xs rounded-full px-2 py-1 {{ $pillCls }}">
                {{ $pillTxt }}
              </span>
            </div>

            <div class="mt-2 flex flex-wrap gap-3 text-xs text-muted-foreground">
              @if(!empty($s->amount))
                <span>{{ $s->amount }}</span>
              @endif

              @if(!empty($s->deadline))
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  {{ $s->deadline }}
                </span>
              @endif
            </div>

            @if(!empty($s->description))
              <p class="text-sm text-muted-foreground mt-2 max-w-3xl break-words">
                {{ $s->description }}
              </p>
            @endif
          </div>
        </div>

        <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:flex-shrink-0 sm:items-center">
          <a href="{{ route('alumni.scholarships.show', $s) }}"
             class="inline-flex items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
            View
          </a>

          @if($applied)
            <button type="button"
                    class="w-full rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground opacity-70 cursor-not-allowed sm:w-auto"
                    disabled>
              Applied
            </button>
          @else
            <form method="POST" action="{{ route('alumni.scholarships.apply', $s) }}">
              @csrf
              <button type="submit"
                      class="w-full rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
                Apply
              </button>
            </form>
          @endif
        </div>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No scholarships available yet.
      </div>
    @endforelse
  </div>

  <div>
    {{ $scholarships->links() }}
  </div>

</div>
@endsection
