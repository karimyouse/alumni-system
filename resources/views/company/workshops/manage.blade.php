@extends('layouts.dashboard')

@php
  $title='Manage Workshop';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">{{ $workshop->title }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ $workshop->date ?? '' }} • {{ $workshop->time ?? '' }} • {{ $workshop->location ?? '' }}
      </p>
    </div>

    <a href="{{ route('company.workshops') }}"
       class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
      Back
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="p-5 border-b border-border">
      <div class="text-lg font-semibold">Registrations</div>
      <div class="text-sm text-muted-foreground">{{ $registrations->count() }} registrations</div>
    </div>

    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Alumnus</th>
            <th class="text-left p-4 font-medium">Academic ID</th>
            <th class="text-left p-4 font-medium">Email</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registrations as $r)
            <tr class="border-b last:border-0">
              <td class="p-4 font-medium">{{ $r->alumni?->name ?? 'Alumni' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $r->alumni?->academic_id ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $r->alumni?->email ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="3">No registrations yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
