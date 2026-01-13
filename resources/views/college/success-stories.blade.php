@extends('layouts.dashboard')

@php
  $title = 'Success Stories';
  $role  = 'College';
  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Alumni','href'=>'/college/alumni','icon'=>'users'],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days'],
    ['label'=>'Jobs','href'=>'/college/jobs','icon'=>'briefcase'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];

  $stories = [
    ['id'=>'1','alumniName'=>'Ahmed Hassan','avatar'=>'AH','title'=>'From Student to Senior Developer','company'=>'TechCorp','year'=>'2020','status'=>'published','views'=>456],
    ['id'=>'2','alumniName'=>'Sara Ali','avatar'=>'SA','title'=>'Building My First Startup','company'=>'StartupX','year'=>'2019','status'=>'published','views'=>312],
    ['id'=>'3','alumniName'=>'Omar Khalil','avatar'=>'OK','title'=>'Career Growth in Global Tech','company'=>'GlobalTech','year'=>'2018','status'=>'draft','views'=>0],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Success Stories</h1>
      <p class="text-muted-foreground">Showcase alumni achievements</p>
    </div>

    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-add-story">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Add Story
    </button>
  </div>

  <div class="grid gap-4">
    @foreach($stories as $s)
      <div class="rounded-xl border border-border bg-card" data-testid="card-story-{{ $s['id'] }}">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
              {{ $s['avatar'] }}
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $s['title'] }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                  {{ $s['status']==='published' ? 'bg-primary/15 text-primary' : 'bg-secondary text-secondary-foreground' }}">
                  {{ $s['status'] }}
                </span>
              </div>

              <p class="text-muted-foreground">{{ $s['alumniName'] }} • {{ $s['company'] }} • Class of {{ $s['year'] }}</p>

              <div class="flex items-center gap-2 mt-2 text-sm text-muted-foreground">
                <i data-lucide="eye" class="h-4 w-4"></i>
                {{ $s['views'] }} views
              </div>
            </div>

            <div class="flex gap-2">
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-edit-{{ $s['id'] }}">
                <i data-lucide="edit" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-delete-{{ $s['id'] }}">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
