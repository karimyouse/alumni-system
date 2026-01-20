@extends('layouts.dashboard')

@php
  $title='Request Rejected';
  $role='Company';
  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
  ];
@endphp

@section('content')
<div class="max-w-2xl">
  <div class="rounded-xl border border-border bg-card p-6">
    <h1 class="text-2xl font-bold mb-2 text-red-400">Request Rejected</h1>
    <p class="text-sm text-muted-foreground mb-4">
      Your company registration <span class="font-semibold text-foreground">{{ $companyName }}</span> was rejected by admin.
    </p>

    @if(!empty($adminNote))
      <div class="rounded-lg border border-border bg-muted/40 p-4 text-sm">
        <div class="font-semibold mb-1">Reason</div>
        <div class="text-muted-foreground">{{ $adminNote }}</div>
      </div>
    @endif

    <div class="mt-4 text-sm text-muted-foreground">
      Status: <span class="text-red-400 font-medium">Rejected</span>
    </div>
  </div>
</div>
@endsection
