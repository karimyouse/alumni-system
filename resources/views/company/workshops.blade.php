@extends('layouts.dashboard')

@php
  $title = 'Workshops';
  $role  = 'Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];

  $workshops = [
    ['id'=>'1','title'=>'Tech Career Day 2026','date'=>'Feb 15, 2026','time'=>'10:00 AM - 2:00 PM','location'=>'Main Campus','status'=>'upcoming','registrations'=>45],
    ['id'=>'2','title'=>'Coding Bootcamp Introduction','date'=>'Jan 28, 2026','time'=>'2:00 PM - 4:00 PM','location'=>'Online','status'=>'upcoming','registrations'=>80],
    ['id'=>'3','title'=>'Industry Insights Session','date'=>'Dec 5, 2025','time'=>'11:00 AM - 1:00 PM','location'=>'Conference Room A','status'=>'completed','registrations'=>35],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-muted-foreground">Participate and manage workshop collaborations</p>
    </div>

    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-propose-workshop">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Propose Workshop
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
                <span class="flex items-center gap-1"><i data-lucide="users" class="h-4 w-4"></i>{{ $w['registrations'] }} registrations</span>
              </div>
            </div>

            <div>
              <button class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50"
                      data-testid="button-manage-{{ $w['id'] }}">
                Manage
              </button>
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
