@extends('layouts.dashboard')

@php
  $title = 'Alumni';
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

  $alumni = [
    ['id'=>'1','name'=>'Ahmed Al-Hassan','academicId'=>'2141091038','email'=>'alumni@ptc.edu','major'=>'Computer Science','year'=>'2024','status'=>'active','employment'=>'Employed'],
    ['id'=>'2','name'=>'Sara Ali','academicId'=>'2141091039','email'=>'sara@ptc.edu','major'=>'Information Technology','year'=>'2024','status'=>'active','employment'=>'Seeking'],
    ['id'=>'3','name'=>'Omar Khalil','academicId'=>'2141091040','email'=>'omar@ptc.edu','major'=>'Computer Science','year'=>'2023','status'=>'active','employment'=>'Employed'],
    ['id'=>'4','name'=>'Layla Hassan','academicId'=>'2141091041','email'=>'layla@ptc.edu','major'=>'Networking','year'=>'2024','status'=>'inactive','employment'=>'Unknown'],
    ['id'=>'5','name'=>'Mohammed Nasser','academicId'=>'2141091042','email'=>'moh@ptc.edu','major'=>'Web Development','year'=>'2023','status'=>'active','employment'=>'Self-employed'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Alumni</h1>
      <p class="text-muted-foreground">Manage and track alumni accounts</p>
    </div>
    <div class="flex items-center gap-2 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-64">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
        <input placeholder="Search alumni..." class="w-full rounded-md border border-input bg-background/60 pl-9 pr-3 py-2 text-sm"
               data-testid="input-search-alumni" />
      </div>
      <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
              data-testid="button-filter">
        <i data-lucide="filter" class="h-4 w-4"></i>
      </button>
    </div>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/50">
          <tr>
            <th class="text-left p-4 font-medium">Alumni</th>
            <th class="text-left p-4 font-medium">Academic ID</th>
            <th class="text-left p-4 font-medium">Major</th>
            <th class="text-left p-4 font-medium">Year</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Employment</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($alumni as $a)
            @php
              $initials = collect(explode(' ', $a['name']))->map(fn($n)=>mb_substr($n,0,1))->join('');
            @endphp
            <tr class="border-b last:border-0" data-testid="row-alumni-{{ $a['id'] }}">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="h-9 w-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                    {{ $initials }}
                  </div>
                  <div>
                    <div class="font-medium">{{ $a['name'] }}</div>
                    <div class="text-xs text-muted-foreground">{{ $a['email'] }}</div>
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-muted-foreground">{{ $a['academicId'] }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $a['major'] }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $a['year'] }}</td>
              <td class="p-4">
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                  {{ $a['status']==='active' ? 'bg-green-500/10 text-green-400' : 'bg-secondary text-secondary-foreground' }}">
                  {{ $a['status'] }}
                </span>
              </td>
              <td class="p-4 text-sm text-muted-foreground">{{ $a['employment'] }}</td>
              <td class="p-4">
                <div class="flex gap-2">
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">View</button>
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Edit</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
