@extends('layouts.dashboard')

@php
  $title='Scholarship Applicants';
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
<div class="space-y-6 max-w-5xl">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-2xl font-bold">{{ $scholarship->title }}</h1>

        @if($scholarship->display_badge)
          <span class="text-[11px] rounded-full px-3 py-1 {{ $scholarship->display_badge['class'] }}">
            {{ $scholarship->display_badge['label'] }}
          </span>
        @endif
      </div>

      <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
        <span class="inline-flex items-center gap-1.5 text-primary font-semibold">
          {{ $scholarship->display_amount }}
        </span>

        <span class="inline-flex items-center gap-1.5">
          <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
          Deadline: {{ $scholarship->display_deadline }}
        </span>

        <span class="inline-flex items-center gap-1.5">
          {{ $scholarship->applications_count }} applicants
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('college.scholarships.edit', $scholarship) }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Edit
      </a>

      <a href="{{ route('college.scholarships') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Back
      </a>
    </div>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="p-5 border-b border-border">
      <div class="text-lg font-semibold">Applicants</div>
      <div class="text-sm text-muted-foreground">{{ $scholarship->applications_count }} application(s)</div>
    </div>

    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Applicant</th>
            <th class="text-left p-4 font-medium">Academic ID</th>
            <th class="text-left p-4 font-medium">Email</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Applied Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($scholarship->applications as $application)
            <tr class="border-b last:border-0">
              <td class="p-4 font-medium">{{ $application->alumni?->name ?? 'Alumni' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $application->alumni?->academic_id ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $application->alumni?->email ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $application->status ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $application->applied_date ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="5">No applicants yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
