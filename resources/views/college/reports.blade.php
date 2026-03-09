@extends('layouts.dashboard')

@php
  $title='Reports';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Manage Alumni','href'=>'/college/alumni','icon'=>'users'],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days'],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase'],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone'],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">Reports</h1>
      <p class="text-sm text-muted-foreground">Analytics and statistical reports</p>
    </div>

    <button type="button"
            onclick="window.print()"
            class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="download" class="h-4 w-4"></i>
      Export Report
    </button>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Total Alumni</div>
        <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="users" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-4xl font-bold mt-4">{{ number_format($totalAlumni) }}</div>
      <div class="text-sm text-muted-foreground mt-1">Registered graduates</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Employment Rate</div>
        <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="user-check" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-4xl font-bold mt-4">{{ $employmentRate }}%</div>
      <div class="text-sm text-muted-foreground mt-1">Employed alumni</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Partner Companies</div>
        <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="building-2" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-4xl font-bold mt-4">{{ number_format($partnerCompanies) }}</div>
      <div class="text-sm text-muted-foreground mt-1">Registered companies</div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">Workshops Held</div>
        <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <i data-lucide="calendar-days" class="h-4 w-4"></i>
        </div>
      </div>
      <div class="text-4xl font-bold mt-4">{{ number_format($workshopsHeld) }}</div>
      <div class="text-sm text-muted-foreground mt-1">Total workshops</div>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border">
        <div class="text-2xl font-semibold inline-flex items-center gap-2">
          <i data-lucide="trending-up" class="h-5 w-5"></i>
          Employment by Industry
        </div>
      </div>

      <div class="p-6 space-y-5">
        @foreach($industryData as $item)
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm">{{ $item['name'] }}</span>
              <span class="text-sm">{{ $item['percent'] }}%</span>
            </div>

            <div class="h-3 rounded-full bg-muted overflow-hidden">
              <div class="h-3 rounded-full bg-primary" style="width: {{ $item['percent'] }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border">
        <div class="text-2xl font-semibold inline-flex items-center gap-2">
          <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          Graduation Year Distribution
        </div>
      </div>

      <div class="p-6 space-y-4">
        @foreach($graduationYearData as $item)
          <div class="grid grid-cols-[70px_1fr] items-center gap-4">
            <div class="text-sm">{{ $item['year'] }}</div>

            <div class="relative h-10 rounded-md bg-muted overflow-hidden">
              <div class="absolute inset-y-0 left-0 rounded-md bg-primary"
                   style="width: {{ $item['percent'] }}%"></div>

              <div class="relative z-10 h-full flex items-center justify-end px-3 text-sm text-primary-foreground font-medium">
                {{ $item['count'] }}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border">
        <div class="text-2xl font-semibold">Content Summary</div>
        <div class="text-sm text-muted-foreground mt-1">Published vs total</div>
      </div>

      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between text-base">
          <span>Announcements</span>
          <span class="font-semibold">{{ $announcementsPublished }}/{{ $announcementsTotal }}</span>
        </div>

        <div class="flex items-center justify-between text-base">
          <span>Success Stories</span>
          <span class="font-semibold">{{ $storiesPublished }}/{{ $storiesTotal }}</span>
        </div>

        <div class="flex items-center justify-between text-base">
          <span>Scholarships</span>
          <span class="font-semibold">{{ $scholarshipsTotal }}</span>
        </div>

        <div class="flex items-center justify-between text-base">
          <span>Jobs</span>
          <span class="font-semibold">{{ $jobsCount }}</span>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card overflow-hidden">
      <div class="p-6 border-b border-border">
        <div class="text-2xl font-semibold">Users Summary</div>
        <div class="text-sm text-muted-foreground mt-1">System roles</div>
      </div>

      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between text-base">
          <span>College Users</span>
          <span class="font-semibold">{{ $collegeUsers }}</span>
        </div>

        <div class="flex items-center justify-between text-base">
          <span>Admins</span>
          <span class="font-semibold">{{ $admins }}</span>
        </div>

        <div class="flex items-center justify-between text-base">
          <span>Companies</span>
          <span class="font-semibold">{{ $partnerCompanies }}</span>
        </div>

        <div class="flex items-center justify-between text-base">
          <span>Alumni</span>
          <span class="font-semibold">{{ $totalAlumni }}</span>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
