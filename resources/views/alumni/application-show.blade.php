@extends('layouts.dashboard')

@php
  $role  = 'Alumni';
  $title = $title ?? 'Application Details';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase'],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days'],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];

  $statusLabel = fn($status) => match(strtolower($status ?? '')) {
    'pending' => __('Pending'),
    'reviewed' => __('Under Review'),
    'accepted' => __('Accepted'),
    'rejected' => __('Rejected'),
    'registered' => __('Registered'),
    'cancelled' => __('Cancelled'),
    default => __($status ?? '—'),
  };
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">{{ __($title) }}</h1>
      <p class="text-sm text-muted-foreground">{{ __('Details and actions for this record') }}</p>
    </div>
    <a href="{{ route('alumni.applications') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
      {{ __('Back') }}
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-6 space-y-4">
    @if($type === 'jobs')
      <div class="text-lg font-semibold">{{ $app->job?->title }}</div>
      <div class="text-sm text-muted-foreground">{{ $app->job?->company_name }}</div>
      <div class="grid md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-muted-foreground">{{ __('Location:') }}</span> {{ $app->job?->location }}</div>
        <div><span class="text-muted-foreground">{{ __('Type:') }}</span> {{ $app->job?->type }}</div>
        <div><span class="text-muted-foreground">{{ __('Salary:') }}</span> {{ $app->job?->salary }}</div>
        <div><span class="text-muted-foreground">{{ __('Applied:') }}</span> {{ $app->applied_date ?? $app->created_at?->format('M d, Y') }}</div>
        <div><span class="text-muted-foreground">{{ __('Status:') }}</span> {{ $statusLabel($app->status) }}</div>
      </div>

    @elseif($type === 'scholarships')
      <div class="text-lg font-semibold">{{ $app->scholarship?->title }}</div>
      <div class="grid md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-muted-foreground">{{ __('Amount:') }}</span> {{ $app->scholarship?->amount }}</div>
        <div><span class="text-muted-foreground">{{ __('Deadline:') }}</span> {{ $app->scholarship?->deadline }}</div>
        <div><span class="text-muted-foreground">{{ __('Applied:') }}</span> {{ $app->applied_date ?? $app->created_at?->format('M d, Y') }}</div>
        <div><span class="text-muted-foreground">{{ __('Status:') }}</span> {{ $statusLabel($app->status) }}</div>
      </div>
      <div class="text-sm text-muted-foreground pt-2">
        {{ $app->scholarship?->description }}
      </div>

    @else
      <div class="text-lg font-semibold">{{ $app->workshop?->title }}</div>
      <div class="grid md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-muted-foreground">{{ __('Date:') }}</span> {{ $app->workshop?->date }}</div>
        <div><span class="text-muted-foreground">{{ __('Time:') }}</span> {{ $app->workshop?->time }}</div>
        <div><span class="text-muted-foreground">{{ __('Location:') }}</span> {{ $app->workshop?->location }}</div>
        <div><span class="text-muted-foreground">{{ __('Status:') }}</span> {{ $statusLabel($app->status) }}</div>
      </div>
    @endif

    <div class="pt-4 flex items-center gap-3">
      <form method="POST" action="{{ route('alumni.applications.withdraw', ['type'=>$type, 'id'=>$app->id]) }}">
        @csrf
        <button class="rounded-md bg-destructive px-4 py-2 text-sm text-white hover:opacity-90">
          {{ $type === 'workshops' ? __('Cancel Registration') : __('Withdraw') }}
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
