@extends('layouts.dashboard')

@php
  $title = __('My Applications');
  $role  = 'Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square','badge'=>$recommendationsReceived ?? 0],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">My Applications</h1>
    <p class="text-sm text-muted-foreground">Track your job, scholarship, and workshop applications</p>
  </div>


  <div class="grid grid-cols-2 rounded-lg bg-muted p-1 gap-1 sm:inline-flex sm:grid-cols-none" id="tabs">
    @foreach($tabs as $i => $t)
      <button
        type="button"
        class="px-3 py-2 text-sm rounded-md {{ $i==0 ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground' }}"
        data-tab="{{ $t['key'] }}"
        data-testid="tab-{{ $t['key'] }}">
        {{ $t['label'] }} ({{ $t['count'] }})
      </button>
    @endforeach
  </div>


  @foreach($tabs as $i => $t)
    @php $list = $itemsByTab[$t['key']] ?? collect(); @endphp

    <div class="space-y-3 tab-panel {{ $i>0 ? 'hidden' : '' }}" data-panel="{{ $t['key'] }}">
      @forelse($list as $it)
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex min-w-0 items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
              <i data-lucide="{{ $it['icon'] }}" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <div class="font-semibold leading-snug break-words">{{ $it['title'] }}</div>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs {{ $it['status_class'] }}">
                  {{ $it['status_label'] }}
                </span>
              </div>

              <div class="text-xs text-muted-foreground mt-1 break-words">
                {{ $it['org'] }} &nbsp;•&nbsp; {{ $it['date_text'] }}
              </div>
            </div>
          </div>

          <a href="{{ route('alumni.applications.show', ['type'=>$it['type'], 'id'=>$it['id']]) }}"
   class="inline-flex w-full items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto sm:flex-shrink-0">
  View Details
</a>
          </div>

        </div>
      @empty
        <div class="rounded-xl border border-border bg-card p-6">
          <div class="text-sm text-muted-foreground">No items in this tab yet.</div>
        </div>
      @endforelse
    </div>
  @endforeach
</div>

<script>
  const tabs = document.getElementById('tabs');
  const btns = tabs.querySelectorAll('[data-tab]');
  const panels = document.querySelectorAll('.tab-panel');

  function setActive(tab) {
    btns.forEach(b => {
      const on = b.dataset.tab === tab;
      b.classList.toggle('bg-background', on);
      b.classList.toggle('shadow-sm', on);
      b.classList.toggle('text-foreground', on);
      b.classList.toggle('text-muted-foreground', !on);
    });
    panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== tab));
  }

  btns.forEach(b => b.addEventListener('click', () => setActive(b.dataset.tab)));
</script>
@endsection
