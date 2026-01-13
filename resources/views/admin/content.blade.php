@extends('layouts.dashboard')

@php
  $title = 'Content';
  $role  = 'Admin';

  $nav = [
    ['label'=>'Overview', 'href'=>'/admin', 'icon'=>'layout-dashboard'],
    ['label'=>'Users',    'href'=>'/admin/users', 'icon'=>'users'],
    ['label'=>'Content',  'href'=>'/admin/content', 'icon'=>'file-text'],
    ['label'=>'Reports',  'href'=>'/admin/reports', 'icon'=>'bar-chart-3'],
    ['label'=>'Settings', 'href'=>'/admin/settings', 'icon'=>'settings'],
    ['label'=>'Support',  'href'=>'/admin/support', 'icon'=>'help-circle'],
  ];

  $pendingContent = [
    'announcements' => [
      ['id'=>'1','title'=>'New Partnership Announcement','author'=>'College Admin','date'=>'Dec 22, 2025'],
    ],
    'successStories' => [
      ['id'=>'2','title'=>'From Intern to CTO','author'=>'Ahmed Hassan','date'=>'Dec 21, 2025'],
    ],
    'workshops' => [
      ['id'=>'3','title'=>'AI Workshop Series','proposedBy'=>'TechCorp','date'=>'Dec 20, 2025'],
    ],
    'scholarships' => [
      ['id'=>'4','title'=>'Innovation Grant 2026','proposedBy'=>'College Admin','date'=>'Dec 19, 2025'],
    ],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Content</h1>
    <p class="text-muted-foreground">Review and approve published content</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div class="font-semibold">Pending Announcements</div>
        <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">{{ count($pendingContent['announcements']) }}</span>
      </div>
      <div class="p-6 space-y-4">
        @foreach($pendingContent['announcements'] as $a)
          <div class="p-4 rounded-lg border border-border">
            <div class="font-medium">{{ $a['title'] }}</div>
            <div class="text-sm text-muted-foreground">{{ $a['author'] }} • {{ $a['date'] }}</div>
            <div class="flex gap-2 mt-3">
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Reject</button>
              <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">Approve</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div class="font-semibold">Pending Success Stories</div>
        <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">{{ count($pendingContent['successStories']) }}</span>
      </div>
      <div class="p-6 space-y-4">
        @foreach($pendingContent['successStories'] as $s)
          <div class="p-4 rounded-lg border border-border">
            <div class="font-medium">{{ $s['title'] }}</div>
            <div class="text-sm text-muted-foreground">{{ $s['author'] }} • {{ $s['date'] }}</div>
            <div class="flex gap-2 mt-3">
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Reject</button>
              <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">Approve</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div class="font-semibold">Pending Workshops</div>
        <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">{{ count($pendingContent['workshops']) }}</span>
      </div>
      <div class="p-6 space-y-4">
        @foreach($pendingContent['workshops'] as $w)
          <div class="p-4 rounded-lg border border-border">
            <div class="font-medium">{{ $w['title'] }}</div>
            <div class="text-sm text-muted-foreground">{{ $w['proposedBy'] }} • {{ $w['date'] }}</div>
            <div class="flex gap-2 mt-3">
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Reject</button>
              <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">Approve</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    
    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border flex items-center justify-between">
        <div class="font-semibold">Pending Scholarships</div>
        <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs">{{ count($pendingContent['scholarships']) }}</span>
      </div>
      <div class="p-6 space-y-4">
        @foreach($pendingContent['scholarships'] as $x)
          <div class="p-4 rounded-lg border border-border">
            <div class="font-medium">{{ $x['title'] }}</div>
            <div class="text-sm text-muted-foreground">{{ $x['proposedBy'] }} • {{ $x['date'] }}</div>
            <div class="flex gap-2 mt-3">
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Reject</button>
              <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">Approve</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</div>
@endsection
