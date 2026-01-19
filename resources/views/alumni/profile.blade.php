@extends('layouts.dashboard')

@php
  $title='My Profile';
  $role='Alumni';

  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase'],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days'],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];

  $skillsArr = collect(explode(',', $profile->skills ?? ''))
      ->map(fn($s)=>trim($s))
      ->filter()
      ->values();
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">My Profile</h1>
    <p class="text-sm text-muted-foreground">Update your public information</p>
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

  <div class="rounded-xl border border-border bg-card p-6">
    <form method="POST" action="{{ route('alumni.profile.update') }}" class="space-y-5">
      @csrf

      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">Full Name</label>
          <input name="name" value="{{ old('name', $user->name) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm" required>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Academic ID</label>
          <input value="{{ $user->academic_id }}" disabled
                 class="w-full rounded-md border border-input bg-background/40 px-3 py-2 text-sm opacity-80">
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">Phone</label>
          <input name="phone" value="{{ old('phone', $profile->phone) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Location</label>
          <input name="location" value="{{ old('location', $profile->location) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
        </div>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">Major</label>
          <input name="major" value="{{ old('major', $profile->major) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Graduation Year</label>
          <input name="graduation_year" value="{{ old('graduation_year', $profile->graduation_year) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">GPA</label>
          <input name="gpa" value="{{ old('gpa', $profile->gpa) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm" placeholder="e.g. 3.50">
        </div>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Skills (comma separated)</label>
        <input name="skills" value="{{ old('skills', $profile->skills) }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="React, Laravel, MySQL, UI/UX">
        <div class="flex flex-wrap gap-2 mt-2">
          @foreach($skillsArr as $sk)
            <span class="text-xs rounded-full border border-border px-3 py-1">{{ $sk }}</span>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium">Bio</label>
        <textarea name="bio" rows="4"
                  class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                  placeholder="Write a short bio...">{{ old('bio', $profile->bio) }}</textarea>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">LinkedIn</label>
          <input name="linkedin" value="{{ old('linkedin', $profile->linkedin) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                 placeholder="https://linkedin.com/in/...">
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Portfolio</label>
          <input name="portfolio" value="{{ old('portfolio', $profile->portfolio) }}"
                 class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                 placeholder="https://...">
        </div>
      </div>

      <div class="pt-2">
        <button class="rounded-md bg-primary px-5 py-2 text-sm text-primary-foreground hover:opacity-90">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
