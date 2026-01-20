@extends('layouts.dashboard')

@php
  $title='Approval Pending';
  $role='Company';
  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
  ];
@endphp

@section('content')
<div class="max-w-2xl">
  <div class="rounded-xl border border-border bg-card p-6">
    <h1 class="text-2xl font-bold mb-2">Approval Pending</h1>
    <p class="text-sm text-muted-foreground mb-4">
      Your company registration <span class="font-semibold text-foreground">{{ $companyName }}</span> is under review.
      You will gain full access once an admin approves your request.
    </p>
    <div class="rounded-lg border border-border bg-muted/40 p-4 text-sm text-muted-foreground">
      Status: <span class="text-foreground font-medium">Pending</span>
    </div>
  </div>
</div>
@endsection
