@extends('layouts.dashboard')

@php
  $title='Scholarships';
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

  $statusPill = fn($s) => match($s) {
    'open' => ['Open','bg-green-500/15 text-green-400'],
    'closing_soon' => ['Closing Soon','bg-orange-500/15 text-orange-400'],
    'closed' => ['Closed','bg-red-500/15 text-red-400'],
    default => [ucfirst($s),'bg-secondary text-secondary-foreground'],
  };
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Scholarships</h1>
      <p class="text-sm text-muted-foreground">Browse scholarships and apply online</p>
    </div>
  </div>

  <div class="space-y-4">
    @foreach($scholarships as $s)
      @php
        [$pillTxt, $pillCls] = $statusPill($s->status ?? 'open');
        $applied = in_array($s->id, $appliedIds ?? []);
      @endphp

      <div class="rounded-xl border border-border bg-card p-6 flex items-center justify-between gap-4">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          </div>

          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-lg font-semibold">{{ $s->title }}</h3>
              <span class="text-xs rounded-full px-2 py-1 {{ $pillCls }}">{{ $pillTxt }}</span>
              @if($applied)
                <span class="text-xs rounded-full px-2 py-1 bg-blue-500/15 text-blue-400">Applied</span>
              @endif
            </div>

            <div class="mt-2 flex flex-wrap gap-3 text-xs text-muted-foreground">
              @if($s->amount)
                <span class="inline-flex items-center gap-1"><i data-lucide="dollar-sign" class="h-3 w-3"></i> {{ $s->amount }}</span>
              @endif
              @if($s->deadline)
                <span class="inline-flex items-center gap-1"><i data-lucide="calendar" class="h-3 w-3"></i> Deadline: {{ $s->deadline }}</span>
              @endif
            </div>

            @if($s->description)
              <p class="text-sm text-muted-foreground mt-2 max-w-3xl">
                {{ $s->description }}
              </p>
            @endif
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('alumni.scholarships.show', $s) }}"
             class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
            Details
          </a>

          @if(!$applied && ($s->status ?? 'open') !== 'closed')
            <form method="POST" action="{{ route('alumni.scholarships.apply', $s) }}">
              @csrf
              <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
                Apply
              </button>
            </form>
          @else
            <button class="rounded-md border border-border px-4 py-2 text-sm opacity-70 cursor-not-allowed" disabled>
              {{ $applied ? 'Applied' : 'Closed' }}
            </button>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  <div>
    {{ $scholarships->links() }}
  </div>
</div>
@endsection
