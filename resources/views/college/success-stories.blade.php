@extends('layouts.dashboard')

@php
  $title = __('Success Stories');
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
      <h1 class="text-2xl font-bold">Success Stories</h1>
      <p class="text-sm text-muted-foreground">Showcase alumni achievements</p>
    </div>

    <a href="{{ route('college.successStories.create') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 sm:w-auto">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Add Story
    </a>
  </div>

  <div class="space-y-4">
    @forelse($stories as $story)
      <div class="rounded-xl border border-border bg-card px-4 py-4 sm:px-5 sm:py-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

          <div class="flex items-start gap-4 min-w-0 flex-1">
            <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-sm font-semibold text-foreground flex-shrink-0">
              {{ $story->display_initials }}
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="font-semibold text-base md:text-lg leading-tight tracking-tight">
                  {{ $story->title }}
                </h3>

                <span class="text-[11px] rounded-full px-3 py-1 {{ $story->display_status_class }}">
                  {{ $story->display_status_label }}
                </span>
              </div>

              <div class="mt-1 text-sm text-muted-foreground break-words">
                {{ $story->name }} - Class of {{ $story->graduation_year }}
              </div>

              <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
                <span>{{ $story->current_position ?: '—' }}</span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                  {{ $story->display_views }} views
                </span>
              </div>
            </div>
          </div>

          <div class="flex w-full items-center gap-2 flex-wrap justify-start sm:w-auto sm:flex-shrink-0 sm:justify-end">
            <a href="{{ route('college.successStories.edit', $story) }}"
               class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
               title="Edit">
              <i data-lucide="file-pen-line" class="h-4 w-4"></i>
            </a>

            <form method="POST" action="{{ route('college.successStories.delete', $story) }}"
                  onsubmit="return confirm('Delete this story?');">
              @csrf
              <button type="submit"
                      class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                      title="Delete">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
            </form>
          </div>

        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No success stories yet.
      </div>
    @endforelse
  </div>

  <div>
    {{ $stories->links() }}
  </div>

</div>
@endsection
