@extends('layouts.dashboard')

@php
  $title = 'Alumni';
  $role  = 'Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];

  $alumni = [
    ['id'=>'1','name'=>'Ahmed Al-Hassan','avatar'=>'AH','major'=>'Computer Science','year'=>2024,'location'=>'Gaza','skills'=>['React','Node.js','TypeScript'],'status'=>'Available'],
    ['id'=>'2','name'=>'Sara Ali','avatar'=>'SA','major'=>'Information Technology','year'=>2023,'location'=>'Ramallah','skills'=>['Python','Data Analysis','SQL'],'status'=>'Available'],
    ['id'=>'3','name'=>'Omar Khalil','avatar'=>'OK','major'=>'Software Engineering','year'=>2022,'location'=>'Gaza','skills'=>['Java','Spring','AWS'],'status'=>'Employed'],
    ['id'=>'4','name'=>'Layla Hassan','avatar'=>'LH','major'=>'Computer Science','year'=>2024,'location'=>'Remote','skills'=>['UI/UX','Figma','Design'],'status'=>'Available'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Alumni</h1>
      <p class="text-muted-foreground">Browse and connect with qualified graduates</p>
    </div>

    <div class="flex items-center gap-2 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-64">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
        <input placeholder="Search by skills, major..." class="w-full rounded-md border border-input bg-background/60 pl-9 pr-3 py-2 text-sm"
               data-testid="input-search-alumni" />
      </div>

      <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
              data-testid="button-filter">
        <i data-lucide="filter" class="h-4 w-4"></i>
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($alumni as $a)
      <div class="rounded-xl border border-border bg-card hover:bg-accent/20 transition"
           data-testid="card-alumni-{{ $a['id'] }}">
        <div class="p-6">
          <div class="flex items-start gap-4">
            <div class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
              {{ $a['avatar'] }}
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2">
                <div>
                  <div class="font-semibold">{{ $a['name'] }}</div>
                  <div class="text-sm text-muted-foreground">{{ $a['major'] }} ({{ $a['year'] }})</div>
                </div>

                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                  {{ $a['status']==='Available' ? 'bg-green-500/10 text-green-400' : 'bg-secondary text-secondary-foreground' }}">
                  {{ $a['status'] }}
                </span>
              </div>

              <div class="flex items-center gap-2 text-xs text-muted-foreground mt-2">
                <i data-lucide="map-pin" class="h-3 w-3"></i>
                {{ $a['location'] }}
              </div>

              <div class="flex flex-wrap gap-1 mt-3">
                @foreach($a['skills'] as $s)
                  <span class="inline-flex items-center rounded-full border border-border px-3 py-1 text-xs">{{ $s }}</span>
                @endforeach
              </div>

              <div class="flex gap-2 mt-4">
                <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50"
                        data-testid="button-view-profile-{{ $a['id'] }}">View Profile</button>

                <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90"
                        data-testid="button-contact-{{ $a['id'] }}">
                  <i data-lucide="mail" class="h-3 w-3 mr-1 inline"></i>
                  Contact
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
