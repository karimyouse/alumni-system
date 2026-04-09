@extends('layouts.dashboard')

@php
  $title = __('Alumni Profile');
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>$applicationBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6 max-w-5xl">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-2xl font-bold">{{ $alumnus->name }}</h1>

        <span class="text-[11px] rounded-full px-3 py-1 {{ $statusClass }}">
          {{ $statusLabel }}
        </span>
      </div>

      <div class="mt-2 text-sm text-muted-foreground">
        {{ $alumnus->academic_id }} • {{ $alumnus->email }}
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="mailto:{{ $alumnus->email }}"
         class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        <i data-lucide="mail" class="h-4 w-4"></i>
        Contact
      </a>

      <a href="{{ route('company.alumni') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Back
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6">
    <div class="rounded-xl border border-border bg-card p-6">
      <div class="flex flex-col items-center text-center">
        @if($photoUrl)
          <img src="{{ $photoUrl }}"
               alt="{{ $alumnus->name }}"
               class="w-24 h-24 rounded-full object-cover border border-border shadow-sm">
        @else
          <div class="w-24 h-24 rounded-full bg-secondary flex items-center justify-center text-3xl font-semibold">
            {{ $initials }}
          </div>
        @endif

        <div class="mt-4 text-xl font-semibold">{{ $alumnus->name }}</div>
        <div class="text-sm text-muted-foreground mt-1">{{ $profile->major ?? '—' }}</div>

        <span class="mt-3 text-xs rounded-full border border-border px-3 py-1">
          Class of {{ $profile->graduation_year ?? '—' }}
        </span>
      </div>

      <div class="mt-6 space-y-3 text-sm">
        <div class="flex items-center gap-2 text-muted-foreground">
          <i data-lucide="mail" class="h-4 w-4"></i>
          <span>{{ $alumnus->email }}</span>
        </div>

        <div class="flex items-center gap-2 text-muted-foreground">
          <i data-lucide="phone" class="h-4 w-4"></i>
          <span>{{ $profile->phone ?? '—' }}</span>
        </div>

        <div class="flex items-center gap-2 text-muted-foreground">
          <i data-lucide="map-pin" class="h-4 w-4"></i>
          <span>{{ $profile->location ?? '—' }}</span>
        </div>

        <div class="flex items-center gap-2 text-muted-foreground">
          <i data-lucide="graduation-cap" class="h-4 w-4"></i>
          <span>ID: {{ $alumnus->academic_id }}</span>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-6">
      <div>
        <h2 class="text-2xl font-semibold">Personal Information</h2>
        <p class="text-sm text-muted-foreground mt-1">Profile details and bio</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div>
          <label class="text-sm font-medium">Full Name</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
            {{ $alumnus->name }}
          </div>
        </div>

        <div>
          <label class="text-sm font-medium">Email</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
            {{ $alumnus->email }}
          </div>
        </div>

        <div>
          <label class="text-sm font-medium">Phone</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
            {{ $profile->phone ?? '—' }}
          </div>
        </div>

        <div>
          <label class="text-sm font-medium">Location</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
            {{ $profile->location ?? '—' }}
          </div>
        </div>

        <div>
          <label class="text-sm font-medium">Major</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
            {{ $profile->major ?? '—' }}
          </div>
        </div>

        <div>
          <label class="text-sm font-medium">GPA</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
            {{ $profile->gpa ?? '—' }}
          </div>
        </div>
      </div>

      <div class="mt-5">
        <label class="text-sm font-medium">Bio</label>
        <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-3 text-sm min-h-[120px] whitespace-pre-line">
          {{ $profile->bio ?? '—' }}
        </div>
      </div>

      <div class="mt-5">
        <label class="text-sm font-medium">Skills</label>
        <div class="mt-3 flex flex-wrap gap-2">
          @forelse($skills as $skillItem)
            <span class="text-xs rounded-full border border-border px-3 py-1">
              {{ $skillItem }}
            </span>
          @empty
            <span class="text-sm text-muted-foreground">No skills listed</span>
          @endforelse
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
        <div>
          <label class="text-sm font-medium">LinkedIn</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm break-all">
            {{ $profile->linkedin ?? '—' }}
          </div>
        </div>

        <div>
          <label class="text-sm font-medium">Portfolio</label>
          <div class="mt-2 rounded-md border border-input bg-background/60 px-3 py-2 text-sm break-all">
            {{ $profile->portfolio ?? '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
