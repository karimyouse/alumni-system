@extends('layouts.dashboard')

@php
  $title = 'Announcements';
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

  $announcements = [
    ['id'=>'1','title'=>'New Scholarship Program Available','date'=>'Dec 22, 2025','status'=>'published','views'=>245],
    ['id'=>'2','title'=>'Career Fair 2026 Registration Open','date'=>'Dec 20, 2025','status'=>'published','views'=>189],
    ['id'=>'3','title'=>'Alumni Networking Event','date'=>'Dec 18, 2025','status'=>'draft','views'=>0],
    ['id'=>'4','title'=>'System Maintenance Notice','date'=>'Dec 15, 2025','status'=>'published','views'=>312],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Announcements</h1>
      <p class="text-muted-foreground">Create and manage announcements</p>
    </div>
    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-add-announcement">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      New Announcement
    </button>
  </div>

  <div class="grid gap-4">
    @foreach($announcements as $a)
      <div class="rounded-xl border border-border bg-card" data-testid="card-announcement-{{ $a['id'] }}">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="megaphone" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $a['title'] }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                  {{ $a['status']==='published' ? 'bg-primary/15 text-primary' : 'bg-secondary text-secondary-foreground' }}">
                  {{ $a['status'] }}
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1"><i data-lucide="calendar" class="h-4 w-4"></i>{{ $a['date'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="eye" class="h-4 w-4"></i>{{ $a['views'] }} views</span>
              </div>
            </div>

            <div class="flex gap-2">
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-edit-{{ $a['id'] }}">
                <i data-lucide="edit" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-delete-{{ $a['id'] }}">
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
