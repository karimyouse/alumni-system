@extends('layouts.dashboard')

@php
  $title = __('Scholarships');
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Browse Alumni','href'=>'/college/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone','badge'=>$announcementBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap','badge'=>$scholarshipBadgeCount ?? 0],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award','badge'=>$successStoryBadgeCount ?? 0],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold">Scholarships</h1>
      <p class="text-sm text-muted-foreground">Manage scholarship programs</p>
    </div>

    <a href="{{ route('college.scholarships.create') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Add Scholarship
    </a>
  </div>

  <form method="GET" action="{{ route('college.scholarships') }}" class="rounded-xl border border-border bg-card p-5">
    <div class="flex flex-col md:flex-row gap-3 md:items-center">
      <div class="flex-1">
        <input name="q" value="{{ $q }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Search by title">
      </div>
      <div class="grid grid-cols-2 gap-2 md:flex">
        <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
          Search
        </button>
        <a href="{{ route('college.scholarships') }}"
           class="inline-flex items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          Reset
        </a>
      </div>
    </div>
  </form>

  <div class="space-y-4">
    @forelse($scholarships as $s)
      <div class="rounded-xl border border-border bg-card px-4 py-4 sm:px-5 sm:py-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

          <div class="flex items-start gap-4 min-w-0 flex-1">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
              <i data-lucide="graduation-cap" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="font-semibold text-base md:text-lg leading-tight tracking-tight">
                  {{ $s->title }}
                </h3>

                @if($s->display_badge)
                  <span class="text-[11px] rounded-full px-3 py-1 {{ $s->display_badge['class'] }}">
                    {{ $s->display_badge['label'] }}
                  </span>
                @endif
              </div>

              <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
                <span class="inline-flex items-center gap-1.5 text-primary font-semibold">
                  {{ $s->display_amount }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
                  Deadline: {{ $s->display_deadline }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  {{ $s->display_applicants }} applicants
                </span>
              </div>
            </div>
          </div>

          <div class="grid w-full grid-cols-[auto_auto_1fr] gap-2 sm:flex sm:w-auto sm:flex-shrink-0 sm:items-center sm:justify-end">
            <a href="{{ route('college.scholarships.edit', $s) }}"
               class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
               title="Edit">
              <i data-lucide="file-pen-line" class="h-4 w-4"></i>
            </a>

            <form method="POST" action="{{ route('college.scholarships.delete', $s) }}"
                  onsubmit="return confirm('Delete this scholarship?');">
              @csrf
              <button class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                      title="Delete">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
            </form>

            <a href="{{ route('college.scholarships.applicants', $s) }}"
               class="inline-flex items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition">
              View Applicants
            </a>
          </div>

        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No scholarships yet.
      </div>
    @endforelse
  </div>

  <div>
    {{ $scholarships->links() }}
  </div>

</div>
@endsection
