@extends('layouts.dashboard')

@php
  $title='Scholarships';
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
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Scholarships</h1>
      <p class="text-sm text-muted-foreground">Create and manage scholarships for alumni</p>
    </div>

    <a href="{{ route('college.scholarships.create') }}"
       class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Add Scholarship
    </a>
  </div>

  {{-- Search --}}
  <form method="GET" action="{{ route('college.scholarships') }}" class="rounded-xl border border-border bg-card p-5">
    <div class="flex flex-col md:flex-row gap-3 md:items-center">
      <div class="flex-1">
        <input name="q" value="{{ $q }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Search by title">
      </div>
      <div class="flex gap-2">
        <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
          Search
        </button>
        <a href="{{ route('college.scholarships') }}"
           class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          Reset
        </a>
      </div>
    </div>
  </form>

  {{-- Table --}}
  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Title</th>
            <th class="text-left p-4 font-medium">Deadline</th>
            <th class="text-left p-4 font-medium">Amount</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($scholarships as $s)
            <tr class="border-b last:border-0">
              <td class="p-4 font-semibold">{{ $s->title }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $s->deadline ?? '—' }}</td>
              <td class="p-4 text-sm text-muted-foreground">{{ $s->amount ?? '—' }}</td>
              <td class="p-4">
                <div class="flex items-center gap-2">
                  <a href="{{ route('college.scholarships.show', $s) }}"
                     class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    Details
                  </a>

                  <form method="POST" action="{{ route('college.scholarships.delete', $s) }}"
                        onsubmit="return confirm('Delete this scholarship?');">
                    @csrf
                    <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="4">No scholarships yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    {{ $scholarships->links() }}
  </div>

</div>
@endsection
