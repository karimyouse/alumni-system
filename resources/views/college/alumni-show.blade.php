@extends('layouts.dashboard')

@php
  $title = __('Alumni Profile');
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

  $p = $alumnus->alumniProfile;
  $skills = collect(explode(',', $p->skills ?? ''))->map(fn($s)=>trim($s))->filter()->values();
@endphp

@section('content')
<div class="space-y-6 max-w-4xl">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">{{ $alumnus->name }}</h1>
      <p class="text-sm text-muted-foreground">{{ $alumnus->academic_id }} • {{ $alumnus->email }}</p>
    </div>
    <a href="{{ route('college.alumni') }}" class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-6 space-y-4">
    <div class="grid md:grid-cols-2 gap-4 text-sm">
      <div><span class="text-muted-foreground">Major:</span> {{ $p->major ?? '—' }}</div>
      <div><span class="text-muted-foreground">Location:</span> {{ $p->location ?? '—' }}</div>
      <div><span class="text-muted-foreground">Graduation Year:</span> {{ $p->graduation_year ?? '—' }}</div>
      <div><span class="text-muted-foreground">GPA:</span> {{ $p->gpa ?? '—' }}</div>
      <div><span class="text-muted-foreground">Phone:</span> {{ $p->phone ?? '—' }}</div>
      <div><span class="text-muted-foreground">LinkedIn:</span> {{ $p->linkedin ?? '—' }}</div>
      <div><span class="text-muted-foreground">Portfolio:</span> {{ $p->portfolio ?? '—' }}</div>
      <div><span class="text-muted-foreground">Employment Status:</span> {{ $p->employment_status ?? '—' }}</div>
    </div>

    @if($p?->bio)
      <div>
        <div class="font-semibold">Bio</div>
        <div class="text-sm text-muted-foreground">{{ $p->bio }}</div>
      </div>
    @endif

    <div>
      <div class="font-semibold">Skills</div>
      <div class="flex flex-wrap gap-2 mt-2">
        @forelse($skills as $sk)
          <span class="text-xs rounded-full border border-border px-3 py-1">{{ $sk }}</span>
        @empty
          <span class="text-sm text-muted-foreground">—</span>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection
