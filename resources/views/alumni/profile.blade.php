@extends('layouts.dashboard')

@php
  $title = 'My Profile';
  $role  = 'Alumni';

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
      ->map(fn($s) => trim($s))
      ->filter()
      ->values();

  $userName = $user->name ?? 'Alumni';
  $initials = collect(explode(' ', $userName))
      ->filter()
      ->map(fn($n) => mb_substr($n, 0, 1))
      ->join('');

  $emailValue = $user->email ?? '';
  $academicId = $user->academic_id ?? '';
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">{{ __('My Profile') }}</h1>
      <p class="text-sm text-muted-foreground">{{ __('Your profile details and public information') }}</p>
    </div>

    <button type="button"
            id="profile-edit-toggle"
            class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 inline-flex items-center gap-2">
      <i data-lucide="pencil" class="h-4 w-4"></i>
      <span id="profile-edit-toggle-text">{{ __('Edit Profile') }}</span>
    </button>
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

  <form method="POST" action="{{ route('alumni.profile.update') }}" id="alumni-profile-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <div class="lg:col-span-1">
        <div class="rounded-xl border border-border bg-card h-full">
          <div class="p-6">
            <div class="flex flex-col items-center text-center">
              <div class="h-24 w-24 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-2xl font-semibold mb-4">
                {{ $initials ?: 'A' }}
              </div>

              <h2 class="text-2xl font-semibold">{{ old('name', $user->name) }}</h2>
              <p class="text-muted-foreground mt-1">{{ old('major', $profile->major ?: __('No major added yet')) }}</p>

              <span class="inline-flex items-center rounded-full bg-secondary px-3 py-1 text-xs mt-3">
                {{ __('Class of') }} {{ old('graduation_year', $profile->graduation_year ?: '—') }}
              </span>

              <div class="w-full mt-6 space-y-4 text-left">
                <div class="flex items-center gap-3 text-sm">
                  <i data-lucide="mail" class="h-4 w-4 text-muted-foreground flex-shrink-0"></i>
                  <span class="truncate">{{ $emailValue ?: '—' }}</span>
                </div>

                <div class="flex items-center gap-3 text-sm">
                  <i data-lucide="phone" class="h-4 w-4 text-muted-foreground flex-shrink-0"></i>
                  <span>{{ old('phone', $profile->phone ?: '—') }}</span>
                </div>

                <div class="flex items-center gap-3 text-sm">
                  <i data-lucide="map-pin" class="h-4 w-4 text-muted-foreground flex-shrink-0"></i>
                  <span>{{ old('location', $profile->location ?: '—') }}</span>
                </div>

                <div class="flex items-center gap-3 text-sm">
                  <i data-lucide="graduation-cap" class="h-4 w-4 text-muted-foreground flex-shrink-0"></i>
                  <span>{{ __('ID:') }} {{ $academicId ?: '—' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-2">
        <div class="rounded-xl border border-border bg-card">
          <div class="p-6 border-b border-border">
            <div class="text-2xl font-semibold">{{ __('Personal Information') }}</div>
            <div class="text-sm text-muted-foreground mt-1">{{ __('Your profile details and bio') }}</div>
          </div>

          <div class="p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Full Name') }}</label>
                <input name="name"
                       value="{{ old('name', $user->name) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       required>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Email') }}</label>
                <input value="{{ $emailValue }}"
                       disabled
                       class="w-full rounded-md border border-input bg-background/40 px-3 py-2 text-sm opacity-80">
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Phone') }}</label>
                <input name="phone"
                       value="{{ old('phone', $profile->phone) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="+970...">
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Location') }}</label>
                <input name="location"
                       value="{{ old('location', $profile->location) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="{{ __('City, Country') }}">
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Major') }}</label>
                <input name="major"
                       value="{{ old('major', $profile->major) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="{{ __('Your major') }}">
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('GPA') }}</label>
                <input name="gpa"
                       value="{{ old('gpa', $profile->gpa) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="3.50">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Academic ID') }}</label>
                <input value="{{ $academicId }}"
                       disabled
                       class="w-full rounded-md border border-input bg-background/40 px-3 py-2 text-sm opacity-80">
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Graduation Year') }}</label>
                <input name="graduation_year"
                       value="{{ old('graduation_year', $profile->graduation_year) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="2026">
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">{{ __('Bio') }}</label>
              <textarea name="bio"
                        rows="4"
                        data-editable="true"
                        disabled
                        class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm min-h-24"
                        placeholder="{{ __('Write a short bio...') }}">{{ old('bio', $profile->bio) }}</textarea>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">{{ __('Skills') }}</label>

              <input name="skills"
                     value="{{ old('skills', $profile->skills) }}"
                     data-editable="true"
                     disabled
                     class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                     placeholder="{{ __('JavaScript, React, Node.js') }}">

              @if($skillsArr->isNotEmpty())
                <div class="flex flex-wrap gap-2 mt-2">
                  @foreach($skillsArr as $skill)
                    <span class="inline-flex items-center rounded-full border border-border px-3 py-1 text-xs">
                      {{ $skill }}
                    </span>
                  @endforeach
                </div>
              @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('LinkedIn') }}</label>
                <input name="linkedin"
                       value="{{ old('linkedin', $profile->linkedin) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="https://linkedin.com/in/...">
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">{{ __('Portfolio') }}</label>
                <input name="portfolio"
                       value="{{ old('portfolio', $profile->portfolio) }}"
                       data-editable="true"
                       disabled
                       class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                       placeholder="https://your-portfolio.com">
              </div>
            </div>

            <div id="profile-save-row" class="hidden pt-2">
              <button type="submit"
                      class="rounded-md bg-primary px-5 py-2 text-sm text-primary-foreground hover:opacity-90 inline-flex items-center gap-2">
                <i data-lucide="save" class="h-4 w-4"></i>
                {{ __('Save Changes') }}
              </button>
            </div>

          </div>
        </div>
      </div>

    </div>
  </form>
</div>

<script>
  (function () {
    const toggleBtn = document.getElementById('profile-edit-toggle');
    const toggleText = document.getElementById('profile-edit-toggle-text');
    const saveRow = document.getElementById('profile-save-row');
    const editableFields = document.querySelectorAll('[data-editable="true"]');

    if (!toggleBtn || !toggleText || !saveRow || !editableFields.length) return;

    let editing = false;

    function applyEditState() {
      editableFields.forEach((field) => {
        field.disabled = !editing;
      });

      saveRow.classList.toggle('hidden', !editing);
      toggleText.textContent = editing ? @json(__('Cancel Editing')) : @json(__('Edit Profile'));
    }

    toggleBtn.addEventListener('click', function () {
      editing = !editing;
      applyEditState();
    });

    applyEditState();
  })();
</script>
@endsection
