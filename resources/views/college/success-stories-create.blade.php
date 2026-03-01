@extends('layouts.dashboard')

@php
  $title='New Success Story';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award'],
  ];
@endphp

@section('content')
<div class="max-w-2xl space-y-6">

  <div>
    <h1 class="text-2xl font-bold">New Success Story</h1>
    <p class="text-sm text-muted-foreground">Create a story and publish it immediately</p>
  </div>

  @if($errors->any())
    <div class="rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
      <div class="font-semibold text-destructive mb-1">Fix the following:</div>
      <ul class="list-disc pl-5 space-y-1 text-destructive/90">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('college.successStories.store') }}"
        class="rounded-xl border border-border bg-card p-6 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-medium">Title *</label>
      <input name="title" required
             class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
             value="{{ old('title') }}" placeholder="From Student to Software Engineer">
    </div>

    <div>
      <label class="text-sm font-medium">Related Alumnus (optional)</label>
      <select name="alumni_user_id"
              class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
        <option value="">— None —</option>
        @foreach($alumni as $a)
          <option value="{{ $a->id }}" {{ old('alumni_user_id')==$a->id ? 'selected' : '' }}>
            {{ $a->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm font-medium">Story *</label>
      <textarea name="body" rows="7" required
                class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                placeholder="Write the success story...">{{ old('body') }}</textarea>
    </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Publish
      </button>
      <a href="{{ route('college.successStories') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Cancel
      </a>
    </div>
  </form>

</div>
@endsection
