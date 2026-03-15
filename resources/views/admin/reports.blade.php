@extends('layouts.dashboard')

@php
  $title = __('Reports');

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $cards = [
    ['label'=>'Total Users','value'=>$totalUsers ?? 0,'sub'=>'All registered users','trend'=>'+12% from last month','icon'=>'users'],
    ['label'=>'Active Jobs','value'=>$activeJobs ?? 0,'sub'=>'Open positions','trend'=>'+8% from last month','icon'=>'briefcase'],
    ['label'=>'Workshops','value'=>$workshops ?? 0,'sub'=>'Held this year','trend'=>null,'icon'=>'calendar-days'],
    ['label'=>'Companies','value'=>$companies ?? 0,'sub'=>'Partner companies','trend'=>'+3% from last month','icon'=>'building-2'],
  ];

  $growthArr = collect($growth ?? [])->map(function ($g) {
    return [
      'm' => $g['label'] ?? '',
      'v' => (int)($g['value'] ?? 0),
    ];
  })->values()->all();

  if (empty($growthArr)) {
    $growthArr = [
      ['m'=>'January','v'=>0],['m'=>'February','v'=>0],['m'=>'March','v'=>0],
      ['m'=>'April','v'=>0],['m'=>'May','v'=>0],['m'=>'June','v'=>0],
    ];
  }

  $colorMap = [
    'Job Applications' => 'bg-primary',
    'Workshop Registrations' => 'bg-green-500',
    'Profile Updates' => 'bg-purple-500',
    'Recommendations Given' => 'bg-orange-500',
    'Scholarship Applications' => 'bg-pink-500',
  ];

  $summaryArr = collect($activity ?? [])->map(function ($v, $k) use ($colorMap) {
    return ['label'=>$k, 'value'=>(int)$v, 'color'=>$colorMap[$k] ?? 'bg-primary'];
  })->values()->all();

  $maxGrowth = collect($growthArr)->max('v') ?: 1;
  $maxSum = collect($summaryArr)->max('value') ?: 1;
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Reports</h1>
      <p class="text-sm text-muted-foreground">System-wide analytics and reports</p>
    </div>

    <div class="flex items-center gap-2">
      <button type="button" onclick="window.print()"
              class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 inline-flex items-center gap-2">
        <i data-lucide="download" class="h-4 w-4"></i>
        Export PDF
      </button>

      <a href="{{ route('admin.reports.exportExcel') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 inline-flex items-center gap-2">
        <i data-lucide="download" class="h-4 w-4"></i>
        Export Excel
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($cards as $c)
      <div class="rounded-xl border border-border bg-card p-5">
        <div class="flex items-center justify-between">
          <div class="text-sm text-muted-foreground">{{ $c['label'] }}</div>
          <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <i data-lucide="{{ $c['icon'] }}" class="h-4 w-4"></i>
          </div>
        </div>
        <div class="text-3xl font-bold mt-3">{{ number_format($c['value']) }}</div>
        <div class="text-xs text-muted-foreground mt-1">{{ $c['sub'] }}</div>
        @if(!empty($c['trend']))
          <div class="text-xs text-green-500 mt-1">{{ $c['trend'] }}</div>
        @endif
      </div>
    @endforeach
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold inline-flex items-center gap-2">
          <i data-lucide="trending-up" class="h-4 w-4"></i>
          User Growth (Monthly)
        </div>
      </div>

      <div class="p-6 space-y-4">
        @foreach($growthArr as $g)
          @php $w = ($g['v'] / $maxGrowth) * 100; @endphp
          <div>
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm">{{ $g['m'] }}</span>
              <span class="text-sm font-medium">{{ $g['v'] }}</span>
            </div>
            <div class="h-2 rounded-full bg-muted overflow-hidden">
              <div class="h-2 rounded-full bg-primary" style="width: {{ $w }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold inline-flex items-center gap-2">
          <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
          Activity Summary
        </div>
      </div>

      <div class="p-6 space-y-4">
        @foreach($summaryArr as $s)
          @php $w = ($s['value'] / $maxSum) * 100; @endphp
          <div>
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm">{{ $s['label'] }}</span>
              <span class="text-sm font-medium">{{ $s['value'] }}</span>
            </div>
            <div class="h-2 rounded-full bg-muted overflow-hidden">
              <div class="h-2 rounded-full {{ $s['color'] }}" style="width: {{ $w }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection
