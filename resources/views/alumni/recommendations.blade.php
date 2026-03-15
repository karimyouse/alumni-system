@extends('layouts.dashboard')

@php
  $title=__('Recommendations');
  $role=__('Alumni');

  $nav = [
    ['label'=>__('Overview'),'href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>__('My Profile'),'href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>__('Job Opportunities'),'href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>__('Workshops'),'href'=>'/alumni/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>__('Scholarships'),'href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>__('Recommendations'),'href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>__('Leaderboard'),'href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>__('My Applications'),'href'=>'/alumni/applications','icon'=>'file-text','badge'=>$applicationsBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">{{ __("Recommendations") }}</h1>
    <p class="text-sm text-muted-foreground">{{ __("Give and receive peer recommendations") }}</p>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-300">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-xl font-semibold inline-flex items-center gap-2">
          <i data-lucide="star" class="h-5 w-5 text-yellow-500"></i>
          {{ __("Received Recommendations") }}
        </div>
      </div>

      <div class="p-6 space-y-4">
        @forelse($received as $rec)
          <div class="p-4 rounded-lg border border-border" data-testid="card-received-rec-{{ $rec->id }}">
            <div class="flex items-start gap-3">
              <div class="h-12 w-12 rounded-full bg-secondary flex items-center justify-center text-sm font-semibold text-foreground flex-shrink-0">
                {{ $rec->initials }}
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                  <div class="min-w-0">
                    <p class="font-semibold">{{ $rec->name }}</p>
                    <p class="text-xs text-muted-foreground">{{ $rec->role_title }}</p>
                  </div>

                  <span class="text-xs text-muted-foreground">{{ $rec->date }}</span>
                </div>

                <p class="text-sm mt-2 text-foreground/90 leading-6">
                  {{ $rec->content }}
                </p>
              </div>
            </div>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">
            {{ __("No received recommendations yet.") }}
          </div>
        @endforelse
      </div>
    </div>


    <div class="rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-xl font-semibold inline-flex items-center gap-2">
          <i data-lucide="send" class="h-5 w-5 text-primary"></i>
          {{ __("Given Recommendations") }}
        </div>
      </div>

      <div class="p-6 space-y-4">
        @forelse($given as $rec)
          <div class="p-4 rounded-lg border border-border" data-testid="card-given-rec-{{ $rec->id }}">
            <div class="flex items-start gap-3">
              <div class="h-12 w-12 rounded-full bg-secondary flex items-center justify-center text-sm font-semibold text-foreground flex-shrink-0">
                {{ $rec->initials }}
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                  <div class="min-w-0">
                    <p class="font-semibold">{{ $rec->name }}</p>
                    <p class="text-xs text-muted-foreground">{{ $rec->role_title }}</p>
                  </div>

                  <span class="text-xs text-muted-foreground">{{ $rec->date }}</span>
                </div>

                <p class="text-sm mt-2 text-foreground/90 leading-6">
                  {{ $rec->content }}
                </p>

                <form method="POST" action="{{ route('alumni.recommendations.destroy', $rec->id) }}" class="mt-3">
                  @csrf
                  @method('DELETE')

                  <button type="submit"
                          class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    {{ __("Delete") }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="text-sm text-muted-foreground">
            {{ __("No given recommendations yet.") }}
          </div>
        @endforelse

        <div class="pt-4 border-t border-border">
          <p class="text-sm font-medium mb-3">{{ __("Write a new recommendation") }}</p>

          <form method="POST" action="{{ route('alumni.recommendations.store') }}" class="space-y-3">
            @csrf

            <select name="to_user_id"
                    class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                    required>
              <option value="">{{ __("Select alumni...") }}</option>
              @foreach($alumniList as $alumni)
                <option value="{{ $alumni->id }}" {{ old('to_user_id') == $alumni->id ? 'selected' : '' }}>
                  {{ $alumni->name }} — {{ $alumni->academic_id ?? '' }} — {{ $alumni->email }}
                </option>
              @endforeach
            </select>

            <input name="role_title"
                   value="{{ old('role_title') }}"
                   class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                   placeholder="{{ __('Your role/title (e.g. Senior Developer at TechCorp)') }}"
                   required>

            <textarea name="content"
                      rows="4"
                      class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm min-h-20"
                      placeholder="{{ __('Search for a peer and write your recommendation...') }}"
                      required>{{ old('content') }}</textarea>

            <button type="submit"
                    class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2"
                    data-testid="button-send-recommendation">
              <i data-lucide="send" class="h-4 w-4"></i>
              {{ __("Send Recommendation") }}
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
