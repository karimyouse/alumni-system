@extends('layouts.dashboard')

@php
  $title = 'Workshops';
  $role  = 'College';

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
<div class="space-y-5">

  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">Workshops</h1>
      <p class="text-sm text-muted-foreground">Manage workshops and events</p>
    </div>

    <a href="{{ route('college.workshops.create') }}"
       class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 transition inline-flex items-center gap-2">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Add Workshop
    </a>
  </div>

  <div class="space-y-4">
    @forelse($workshops as $w)
      @php
        $statusClass = match($w->display_status) {
          'completed' => 'bg-secondary text-secondary-foreground',
          'cancelled' => 'bg-red-500/10 text-red-400',
          default => 'bg-primary text-primary-foreground',
        };
      @endphp

      <div class="rounded-xl border border-border bg-card px-5 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap lg:flex-nowrap">

          <div class="flex items-center gap-4 min-w-0 flex-1">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
              <i data-lucide="calendar-days" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="font-semibold text-base md:text-lg leading-tight tracking-tight">
                  {{ $w->title }}
                </h3>

                <span class="text-[11px] rounded-full px-3 py-1 {{ $statusClass }}">
                  {{ ucfirst($w->display_status) }}
                </span>
              </div>

              <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
                  {{ $w->date }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                  {{ $w->time }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                  {{ $w->location }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="users" class="h-3.5 w-3.5"></i>
                  {{ $w->display_spots_label }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 flex-wrap justify-end">
            <a href="{{ route('college.workshops.edit', $w) }}"
               class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
               title="Edit Workshop">
              <i data-lucide="pencil" class="h-4 w-4"></i>
            </a>

            <form method="POST" action="{{ route('college.workshops.delete', $w) }}"
                  onsubmit="return confirm('Are you sure you want to delete this workshop?');">
              @csrf
              <button type="submit"
                      class="h-10 w-10 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50 transition"
                      title="Delete Workshop">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
              </button>
            </form>

            <a href="{{ route('college.workshops.manage', $w) }}"
               class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition">
              View Registrations
            </a>
          </div>

        </div>
      </div>
    @empty
      <div class="rounded-xl border border-border bg-card p-6 text-sm text-muted-foreground">
        No workshops yet. Click <b>Add Workshop</b> to create one.
      </div>
    @endforelse
  </div>

  @if(method_exists($workshops, 'links'))
    <div class="pt-2">
      {{ $workshops->links() }}
    </div>
  @endif

</div>
@endsection
