@extends('layouts.dashboard')

@php
  $title = 'Reports';
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

  $industry = [
    ['name'=>'Technology','pct'=>45],
    ['name'=>'Healthcare','pct'=>20],
    ['name'=>'Education','pct'=>15],
    ['name'=>'Finance','pct'=>12],
    ['name'=>'Other','pct'=>8],
  ];

  $graduationByYear = [
    ['year'=>'2024','count'=>320],
    ['year'=>'2023','count'=>285],
    ['year'=>'2022','count'=>250],
    ['year'=>'2021','count'=>220],
    ['year'=>'2020','count'=>175],
  ];

  $maxYear = 320;
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Reports</h1>
      <p class="text-muted-foreground">Analytics and insights on alumni outcomes</p>
    </div>

    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-export-report">
      <i data-lucide="download" class="h-4 w-4 mr-2 inline"></i>
      Export Report
    </button>
  </div>


  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Alumni</div>
        <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">1,250</div>
      <div class="text-xs text-muted-foreground mt-1">Registered graduates</div>
      <div class="text-xs text-green-500 mt-1">▲ 8%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Employment Rate</div>
        <i data-lucide="user-check" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">78%</div>
      <div class="text-xs text-muted-foreground mt-1">Last 6 months</div>
      <div class="text-xs text-green-500 mt-1">▲ 5%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Partner Companies</div>
        <i data-lucide="building-2" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">45</div>
      <div class="text-xs text-muted-foreground mt-1">Active partnerships</div>
      <div class="text-xs text-green-500 mt-1">▲ 3%</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Workshops Held</div>
        <i data-lucide="calendar-days" class="h-4 w-4 text-muted-foreground"></i>
      </div>
      <div class="text-3xl font-bold mt-3">24</div>
      <div class="text-xs text-muted-foreground mt-1">This year</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="font-semibold inline-flex items-center gap-2">
          <i data-lucide="trending-up" class="h-5 w-5"></i>
          Employment by Industry
        </div>
      </div>
      <div class="p-6 space-y-4">
        @foreach($industry as $i)
          <div>
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm">{{ $i['name'] }}</span>
              <span class="text-sm font-medium">{{ $i['pct'] }}%</span>
            </div>
            <div class="h-2 rounded-full bg-muted overflow-hidden">
              <div class="h-2 rounded-full bg-primary/80" style="width: {{ $i['pct'] }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    
    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="font-semibold inline-flex items-center gap-2">
          <i data-lucide="bar-chart-3" class="h-5 w-5"></i>
          Graduates by Year
        </div>
      </div>
      <div class="p-6 space-y-4">
        @foreach($graduationByYear as $g)
          @php $w = round(($g['count'] / $maxYear) * 100, 2); @endphp
          <div class="flex items-center gap-4">
            <span class="w-12 text-sm font-medium">{{ $g['year'] }}</span>
            <div class="flex-1 h-8 bg-muted rounded-md overflow-hidden">
              <div class="h-full bg-primary/80 rounded-md flex items-center justify-end pr-2" style="width: {{ $w }}%">
                <span class="text-xs text-primary-foreground font-medium">{{ $g['count'] }}</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
