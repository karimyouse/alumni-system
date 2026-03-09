@extends('layouts.dashboard')

@php
  $title='Announcements';
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

  $statusMeta = fn($isPublished) => $isPublished
    ? ['published', 'bg-primary text-primary-foreground']
    : ['draft', 'bg-secondary text-secondary-foreground'];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">Announcements</h1>
      <p class="text-sm text-muted-foreground">Create and manage announcements</p>
    </div>

    <a href="{{ route('college.announcements.create') }}"
       class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4"></i>
      New Announcement
    </a>
  </div>

  <div class="space-y-4">
    @forelse($announcements as $a)
      @php
        [$statusLabel, $statusClass] = $statusMeta((bool) $a->is_published);
        $displayDate = $a->published_at?->format('M d, Y') ?? $a->created_at?->format('M d, Y');
        $views = isset($a->views_count) ? (int) $a->views_count : 0;
      @endphp

      <div class="rounded-xl border border-border bg-card px-5 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap lg:flex-nowrap">

          <div class="flex items-center gap-4 min-w-0 flex-1">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
              <i data-lucide="megaphone" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="font-semibold text-base md:text-lg leading-tight tracking-tight">
                  {{ $a->title }}
                </h3>

                <span class="text-[11px] rounded-full px-3 py-1 {{ $statusClass }}">
                  {{ $statusLabel }}
                </span>
              </div>

              <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
                <span>{{ $displayDate ?? '—' }}</span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                  {{ $views }} views
                </span>
              </div>

              @if(!empty($a->body))
                <div class="mt-2 text-sm text-muted-foreground line-clamp-2">
                  {{ $a->body }}
                </div>
              @endif
            </div>
          </div>

          <div class="flex items-center gap-2 flex-wrap justify-end">
            <a href="{{ route('college.announcements.edit', $a) }}"
               class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
               title="Edit">
              <i data-lucide="file-pen-line" class="h-4 w-4"></i>
            </a>

            <form method="POST" action="{{ route('college.announcements.delete', $a) }}"
                  onsubmit="return confirm('Delete this announcement?');">
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
        No announcements yet.
      </div>
    @endforelse
  </div>

  <div>
    {{ $announcements->links() }}
  </div>

</div>
@endsection
