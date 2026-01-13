@extends('layouts.dashboard')

@php
  $title = 'Scholarships';
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

  $scholarships = [
    ['id'=>'1','title'=>'Graduate Excellence Award','amount'=>'$5,000','deadline'=>'Feb 15, 2026','applicants'=>28,'status'=>'open'],
    ['id'=>'2','title'=>'Tech Innovation Scholarship','amount'=>'$3,000','deadline'=>'Mar 1, 2026','applicants'=>15,'status'=>'open'],
    ['id'=>'3','title'=>'Community Leadership Grant','amount'=>'$2,500','deadline'=>'Mar 15, 2026','applicants'=>12,'status'=>'open'],
    ['id'=>'4','title'=>'Research Excellence Fund','amount'=>'$4,000','deadline'=>'Jan 30, 2026','applicants'=>8,'status'=>'closing_soon'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Scholarships</h1>
      <p class="text-muted-foreground">Manage scholarship programs</p>
    </div>

    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90"
            data-testid="button-add-scholarship">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      Add Scholarship
    </button>
  </div>

  <div class="grid gap-4">
    @foreach($scholarships as $s)
      <div class="rounded-xl border border-border bg-card" data-testid="card-scholarship-{{ $s['id'] }}">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
              <i data-lucide="graduation-cap" class="h-6 w-6 text-primary"></i>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold">{{ $s['title'] }}</h3>
                @if($s['status']==='closing_soon')
                  <span class="inline-flex items-center rounded-full bg-red-500/15 text-red-400 px-2 py-1 text-xs">
                    Closing Soon
                  </span>
                @endif
              </div>

              <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                <span class="text-primary font-semibold">{{ $s['amount'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="calendar" class="h-4 w-4"></i>Deadline: {{ $s['deadline'] }}</span>
                <span class="flex items-center gap-1"><i data-lucide="users" class="h-4 w-4"></i>{{ $s['applicants'] }} applicants</span>
              </div>
            </div>

            <div class="flex gap-2">
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-edit-{{ $s['id'] }}">
                <i data-lucide="edit" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                      data-testid="button-delete-{{ $s['id'] }}">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
              <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50"
                      data-testid="button-view-applicants-{{ $s['id'] }}">
                View Applicants
              </button>
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
