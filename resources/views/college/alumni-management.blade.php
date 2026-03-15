@extends('layouts.dashboard')

@php
  $title = __('Alumni Management');
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Manage Alumni','href'=>'/college/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone','badge'=>$announcementBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap','badge'=>$scholarshipBadgeCount ?? 0],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award','badge'=>$successStoryBadgeCount ?? 0],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Alumni</h1>
      <p class="text-sm text-muted-foreground">Search and view alumni profiles</p>
    </div>
  </div>

  <form method="GET" action="{{ route('college.alumni') }}" class="rounded-xl border border-border bg-card p-5">
    <div class="flex flex-col md:flex-row gap-3 md:items-center">
      <div class="flex-1">
        <input name="q" value="{{ $q }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Search by name, email, or academic ID">
      </div>
      <div class="flex gap-2">
        <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
          Search
        </button>
        <a href="{{ route('college.alumni') }}"
           class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          Reset
        </a>
      </div>
    </div>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($alumni as $a)
      @php
        $p = $a->alumniProfile;
        $initials = collect(explode(' ', $a->name))->map(fn($n)=>mb_substr($n,0,1))->join('');
        $major = $p->major ?? '—';
        $year = $p->graduation_year ?? '—';
        $status = $p->employment_status ?? '—';
        $employed = strtolower((string)$status) === 'employed';
      @endphp

      <a href="{{ route('college.alumni.show', $a) }}"
         class="block rounded-xl border border-border bg-card p-5 hover:shadow-sm transition">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
            {{ $initials }}
          </div>
          <div class="min-w-0 flex-1">
            <div class="font-semibold truncate">{{ $a->name }}</div>
            <div class="text-xs text-muted-foreground truncate">{{ $a->academic_id }} • {{ $a->email }}</div>
          </div>

          <span class="text-xs rounded-full px-3 py-1
            {{ $employed ? 'bg-green-500/10 text-green-400' : 'bg-secondary text-secondary-foreground' }}">
            {{ $status }}
          </span>
        </div>

        <div class="mt-4 text-sm text-muted-foreground">
          {{ $major }} • Class of {{ $year }}
        </div>
      </a>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No alumni found.
      </div>
    @endforelse
  </div>

  <div>
    {{ $alumni->links() }}
  </div>

</div>
@endsection
