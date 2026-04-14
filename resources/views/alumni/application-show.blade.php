@extends('layouts.dashboard')

@php
  $role  = 'Alumni';
  $title = $title ?? __('Application Details');

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
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold leading-tight break-words">{{ $title }}</h1>
      <p class="text-sm text-muted-foreground">Details and actions for this record</p>
    </div>
    <a href="{{ route('alumni.applications') }}"
       class="inline-flex w-full items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card p-4 space-y-4 sm:p-6">
    @if($type === 'jobs')
      <div class="text-lg font-semibold break-words">{{ $app->job?->title }}</div>
      <div class="text-sm text-muted-foreground break-words">{{ $app->job?->company_name }}</div>
      <div class="grid md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-muted-foreground">Location:</span> {{ $app->job?->location }}</div>
        <div><span class="text-muted-foreground">Type:</span> {{ $app->job?->type }}</div>
        <div><span class="text-muted-foreground">Salary:</span> {{ $app->job?->salary }}</div>
        <div><span class="text-muted-foreground">Applied:</span> {{ $app->applied_date ?? $app->created_at?->format('M d, Y') }}</div>
        <div><span class="text-muted-foreground">Status:</span> {{ ucfirst($app->status) }}</div>
      </div>

      @if($app->job?->company || $app->job?->company_name)
        @include('partials.company-trust-card', [
          'company' => $app->job?->company,
          'fallbackName' => $app->job?->company_name,
          'variant' => 'embedded',
        ])
      @endif

    @elseif($type === 'scholarships')
      <div class="text-lg font-semibold break-words">{{ $app->scholarship?->title }}</div>
      <div class="grid md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-muted-foreground">Amount:</span> {{ $app->scholarship?->amount }}</div>
        <div><span class="text-muted-foreground">Deadline:</span> {{ $app->scholarship?->deadline }}</div>
        <div><span class="text-muted-foreground">Applied:</span> {{ $app->applied_date ?? $app->created_at?->format('M d, Y') }}</div>
        <div><span class="text-muted-foreground">Status:</span> {{ ucfirst($app->status) }}</div>
      </div>
      <div class="text-sm text-muted-foreground pt-2 break-words">
        {{ $app->scholarship?->description }}
      </div>

    @else
      <div class="text-lg font-semibold break-words">{{ $app->workshop?->title }}</div>
      <div class="grid md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-muted-foreground">Date:</span> {{ $app->workshop?->date }}</div>
        <div><span class="text-muted-foreground">Time:</span> {{ $app->workshop?->time }}</div>
        <div><span class="text-muted-foreground">Location:</span> {{ $app->workshop?->location }}</div>
        <div><span class="text-muted-foreground">Status:</span> {{ ucfirst($app->status) }}</div>
      </div>

      @if($app->workshop?->company || $app->workshop?->company_user_id)
        @include('partials.company-trust-card', [
          'company' => $app->workshop?->company,
          'fallbackName' => $app->workshop?->company?->name,
          'variant' => 'embedded',
        ])
      @endif
    @endif

    <div class="pt-4 grid grid-cols-1 gap-3 sm:flex sm:items-center">
      <form method="POST" action="{{ route('alumni.applications.withdraw', ['type'=>$type, 'id'=>$app->id]) }}">
        @csrf
        <button class="w-full rounded-md bg-destructive px-4 py-2 text-sm text-white hover:opacity-90 sm:w-auto">
          {{ $type === 'workshops' ? 'Cancel Registration' : 'Withdraw' }}
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
