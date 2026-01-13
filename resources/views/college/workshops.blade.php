@extends('layouts.dashboard')

@php
  $title = 'Workshops';
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

  $workshops = [
    ['id'=>'1','title'=>'Career Development Workshop','date'=>'Jan 15, 2026','time'=>'10:00 AM - 2:00 PM','location'=>'Main Campus','registrations'=>35,'capacity'=>50,'status'=>'upcoming'],
    ['id'=>'2','title'=>'Technical Interview Prep','date'=>'Jan 20, 2026','time'=>'2:00 PM - 5:00 PM','location'=>'Online','registrations'=>70,'capacity'=>100,'status'=>'upcoming'],
    ['id'=>'3','title'=>'Resume Writing Masterclass','date'=>'Jan 25, 2026','time'=>'11:00 AM - 1:00 PM','location'=>'Lab B','registrations'=>25,'capacity'=>30,'status'=>'upcoming'],
    ['id'=>'4','title'=>'Networking Skills','date'=>'Dec 10, 2025','time'=>'3:00 PM - 6:00 PM','location'=>'Community Center','registrations'=>40,'capacity'=>40,'status'=>'completed'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-muted-foreground">Manage workshops and events</p>
    </div>

    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-add-workshop">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Add Workshop
    </button>
  </div>

  <div class="grid gap-4">
    @foreach($workshops as $w)
      <div class="rounded-xl border border-border bg-card" data-testid="card-workshop-{{ $w['id'] }}">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="calendar-days" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $w['title'] }}</h3>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                  {{ $w['status']==='upcoming' ? 'bg-primary/15 text-primary' : 'bg-secondary text-secondary-foreground' }}">
                  {{ $w['status'] }}
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="flex items-center gap-1"><i data-lucide="calendar-days" class="h-4 w-4"></i>{{ $w['date'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="clock" class="h-4 w-4"></i>{{ $w['time'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="map-pin" class="h-4 w-4"></i>{{ $w['location'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="users" class="h-4 w-4"></i>{{ $w['registrations'] }}/{{ $w['capacity'] }} registered</span>
              </div>
            </div>

            <div class="flex gap-2">
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-edit-{{ $w['id'] }}">
                <i data-lucide="edit" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-delete-{{ $w['id'] }}">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50"
                      data-testid="button-view-registrations-{{ $w['id'] }}">
                View Registrations
              </button>
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
