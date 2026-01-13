@extends('layouts.dashboard')

@php
  $title = 'Reports';
  $role  = 'Admin';

  $nav = [
    ['label'=>'Overview', 'href'=>'/admin', 'icon'=>'layout-dashboard'],
    ['label'=>'Users',    'href'=>'/admin/users', 'icon'=>'users'],
    ['label'=>'Content',  'href'=>'/admin/content', 'icon'=>'file-text'],
    ['label'=>'Reports',  'href'=>'/admin/reports', 'icon'=>'bar-chart-3'],
    ['label'=>'Settings', 'href'=>'/admin/settings', 'icon'=>'settings'],
    ['label'=>'Support',  'href'=>'/admin/support', 'icon'=>'help-circle'],
  ];

  $userGrowth = [
    ['month'=>'January','users'=>120],
    ['month'=>'February','users'=>145],
    ['month'=>'March','users'=>180],
    ['month'=>'April','users'=>210],
    ['month'=>'May','users'=>250],
    ['month'=>'June','users'=>285],
  ];
  $maxUsers = max(array_column($userGrowth,'users'));

  $activitySummary = [
    ['activity'=>'Job Applications','count'=>456,'bar'=>'bg-blue-500'],
    ['activity'=>'Workshop Registrations','count'=>312,'bar'=>'bg-green-500'],
    ['activity'=>'Profile Updates','count'=>289,'bar'=>'bg-purple-500'],
    ['activity'=>'Recommendations Given','count'=>145,'bar'=>'bg-orange-500'],
    ['activity'=>'Scholarship Applications','count'=>78,'bar'=>'bg-pink-500'],
  ];
  $maxAct = max(array_column($activitySummary,'count'));
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Reports</h1>
      <p class="text-muted-foreground">System analytics and activity summary</p>
    </div>
    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="download" class="h-4 w-4 mr-2 inline"></i>
      Export
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold inline-flex items-center gap-2">
          <i data-lucide="users" class="h-5 w-5"></i>
          User Growth
        </div>
      </div>
      <div class="p-6 space-y-4">
        @foreach($userGrowth as $item)
          @php $w = round(($item['users'] / $maxUsers) * 100, 2); @endphp
          <div class="flex items-center gap-4">
            <span class="w-20 text-sm">{{ $item['month'] }}</span>
            <div class="flex-1 h-6 bg-muted rounded-md overflow-hidden">
              <div class="h-full bg-primary/80 rounded-md flex items-center justify-end pr-2" style="width: {{ $w }}%">
                <span class="text-xs text-primary-foreground font-medium">{{ $item['users'] }}</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    
    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold inline-flex items-center gap-2">
          <i data-lucide="activity" class="h-5 w-5"></i>
          Activity Summary
        </div>
      </div>

      <div class="p-6 space-y-4">
        @foreach($activitySummary as $a)
          @php $w = round(($a['count'] / $maxAct) * 100, 2); @endphp
          <div class="space-y-1">
            <div class="flex justify-between text-sm">
              <span>{{ $a['activity'] }}</span>
              <span class="text-muted-foreground">{{ $a['count'] }}</span>
            </div>
            <div class="h-2 bg-muted rounded-full overflow-hidden">
              <div class="h-2 {{ $a['bar'] }} rounded-full" style="width: {{ $w }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</div>
@endsection
