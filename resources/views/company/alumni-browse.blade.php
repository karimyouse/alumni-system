@extends('layouts.dashboard')

@php
  $title='Browse Alumni';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Browse Alumni</h1>
    <p class="text-sm text-muted-foreground">Search and filter alumni profiles</p>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('company.alumni') }}" class="rounded-xl border border-border bg-card p-5 space-y-4">
    <div class="grid md:grid-cols-4 gap-3">
      <div class="space-y-2">
        <label class="text-sm font-medium">Search</label>
        <input name="q" value="{{ $q }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Name / Email / Academic ID">
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Major</label>
        <select name="major" class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
          <option value="">All</option>
          @foreach($majors as $m)
            <option value="{{ $m }}" {{ $major===$m?'selected':'' }}>{{ $m }}</option>
          @endforeach
        </select>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Location</label>
        <select name="location" class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
          <option value="">All</option>
          @foreach($locations as $l)
            <option value="{{ $l }}" {{ $location===$l?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Skill</label>
        <input name="skill" value="{{ $skill }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="e.g. Laravel">
      </div>
    </div>

    <div class="flex items-center gap-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Apply Filters
      </button>

      <a href="{{ route('company.alumni') }}" class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Reset
      </a>
    </div>
  </form>

  {{-- List --}}
  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Alumnus</th>
            <th class="text-left p-4 font-medium">Major</th>
            <th class="text-left p-4 font-medium">Location</th>
            <th class="text-left p-4 font-medium">Skills</th>
            <th class="text-left p-4 font-medium">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($alumni as $a)
            @php
              $p = $a->alumniProfile;
              $skills = collect(explode(',', $p->skills ?? ''))->map(fn($s)=>trim($s))->filter()->take(4)->values();
            @endphp
            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $a->name }}</div>
                <div class="text-xs text-muted-foreground">{{ $a->academic_id }} • {{ $a->email }}</div>
              </td>

              <td class="p-4 text-sm text-muted-foreground">{{ $p->major ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $p->location ?? '—' }}</td>

              <td class="p-4">
                <div class="flex flex-wrap gap-2">
                  @forelse($skills as $sk)
                    <span class="text-xs rounded-full border border-border px-2 py-1">{{ $sk }}</span>
                  @empty
                    <span class="text-sm text-muted-foreground">—</span>
                  @endforelse
                </div>
              </td>

              <td class="p-4">
                <a href="{{ route('company.alumni.show', $a) }}"
                   class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                  View Profile
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="5">No alumni found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    {{ $alumni->links() }}
  </div>

</div>
@endsection
