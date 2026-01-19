@extends('layouts.dashboard')

@php
  $title='Recommendations';
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
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Recommendations</h1>
      <p class="text-sm text-muted-foreground">Send and manage recommendations</p>
    </div>
    <button id="toggleForm" class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Send Recommendation
    </button>
  </div>

  {{-- Send form --}}
  <div id="sendForm" class="hidden rounded-xl border border-border bg-card p-6 space-y-4">
    <form method="POST" action="{{ route('alumni.recommendations.store') }}" class="space-y-4">
      @csrf

      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">To (Alumni)</label>
          <select name="to_user_id" class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm" required>
            <option value="">Select alumni...</option>
            @foreach($alumniList as $a)
              <option value="{{ $a->id }}">
                {{ $a->name }} — {{ $a->academic_id ?? '' }} — {{ $a->email }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Your Role / Title</label>
          <input name="role_title" class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                 placeholder="e.g. Senior Developer at TechCorp" required>
        </div>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Recommendation</label>
        <textarea name="content" rows="4"
                  class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                  placeholder="Write your recommendation..." required></textarea>
      </div>

      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Send
      </button>
    </form>
  </div>

  {{-- Tabs --}}
  <div class="inline-flex rounded-lg bg-muted p-1 gap-1" id="tabsRec">
    <button class="px-3 py-2 text-sm rounded-md bg-background shadow-sm text-foreground" data-tab="received">
      Received ({{ $received->count() }})
    </button>
    <button class="px-3 py-2 text-sm rounded-md text-muted-foreground hover:text-foreground" data-tab="given">
      Given ({{ $given->count() }})
    </button>
  </div>

  {{-- Received --}}
  <div class="space-y-3 rec-panel" data-panel="received">
    @forelse($received as $r)
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-semibold">{{ $r->from_name }}</div>
            <div class="text-sm text-muted-foreground">{{ $r->role_title }} • {{ $r->date }}</div>
            <div class="text-sm mt-3 text-muted-foreground">{{ $r->content }}</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            <i data-lucide="message-square" class="h-5 w-5"></i>
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">No received recommendations yet.</div>
    @endforelse
  </div>

  {{-- Given --}}
  <div class="space-y-3 rec-panel hidden" data-panel="given">
    @forelse($given as $r)
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-semibold">To: {{ $r->to_name }}</div>
            <div class="text-sm text-muted-foreground">{{ $r->role_title }} • {{ $r->date }}</div>
            <div class="text-sm mt-3 text-muted-foreground">{{ $r->content }}</div>

            <form method="POST" action="{{ route('alumni.recommendations.destroy', $r) }}" class="mt-4">
              @csrf
              @method('DELETE')
              <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
                Delete
              </button>
            </form>
          </div>
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            <i data-lucide="send" class="h-5 w-5"></i>
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">No given recommendations yet.</div>
    @endforelse
  </div>
</div>

<script>
  // toggle form
  const toggleBtn = document.getElementById('toggleForm');
  const formBox = document.getElementById('sendForm');
  toggleBtn?.addEventListener('click', () => formBox.classList.toggle('hidden'));

  // tabs
  const tabs = document.getElementById('tabsRec');
  const btns = tabs.querySelectorAll('[data-tab]');
  const panels = document.querySelectorAll('.rec-panel');

  function setTab(tab) {
    btns.forEach(b => {
      const on = b.dataset.tab === tab;
      b.classList.toggle('bg-background', on);
      b.classList.toggle('shadow-sm', on);
      b.classList.toggle('text-foreground', on);
      b.classList.toggle('text-muted-foreground', !on);
    });
    panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== tab));
  }
  btns.forEach(b => b.addEventListener('click', () => setTab(b.dataset.tab)));
</script>
@endsection
