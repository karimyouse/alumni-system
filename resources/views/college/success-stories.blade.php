@extends('layouts.dashboard')

@php
  $title='Success Stories';
  $role='College';

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

  $pill = fn($is) => $is
    ? ['Published','bg-green-500/15 text-green-400']
    : ['Unpublished','bg-muted text-foreground'];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Success Stories</h1>
      <p class="text-sm text-muted-foreground">Create and publish alumni success stories</p>
    </div>

    <a href="{{ route('college.successStories.create') }}"
       class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4"></i>
      New Story
    </a>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Title</th>
            <th class="text-left p-4 font-medium">Published</th>
            <th class="text-left p-4 font-medium">Created</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stories as $s)
            @php [$stLabel,$stClass] = $pill((bool)$s->is_published); @endphp
            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $s->title }}</div>
                <div class="text-xs text-muted-foreground mt-1 line-clamp-1">{{ $s->body }}</div>
              </td>

              <td class="p-4">
                <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ $stLabel }}</span>
              </td>

              <td class="p-4 text-sm text-muted-foreground">{{ $s->created_at?->format('M d, Y') }}</td>

              <td class="p-4">
                <form method="POST" action="{{ route('college.successStories.toggle', $s) }}">
                  @csrf
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    {{ $s->is_published ? 'Unpublish' : 'Publish' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="4">No success stories yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    {{ $stories->links() }}
  </div>

</div>
@endsection
