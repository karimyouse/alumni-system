@extends('layouts.dashboard')

@php
  $title = 'My Profile';
  $role  = 'Alumni';
  $nav = [
    ['label'=>'Overview','href'=>'/alumni','icon'=>'layout-dashboard'],
    ['label'=>'My Profile','href'=>'/alumni/profile','icon'=>'user'],
    ['label'=>'Job Opportunities','href'=>'/alumni/jobs','icon'=>'briefcase','badge'=>12],
    ['label'=>'Workshops','href'=>'/alumni/workshops','icon'=>'calendar-days','badge'=>3],
    ['label'=>'Scholarships','href'=>'/alumni/scholarships','icon'=>'graduation-cap'],
    ['label'=>'Recommendations','href'=>'/alumni/recommendations','icon'=>'message-square'],
    ['label'=>'Leaderboard','href'=>'/alumni/leaderboard','icon'=>'trophy'],
    ['label'=>'My Applications','href'=>'/alumni/applications','icon'=>'file-text'],
  ];

  $u = auth()->user();
  $profile = [
    'name' => $u->name ?? 'Karim Shafiq',
    'academicId' => $u->academic_id ?? '2141091038',
    'email' => $u->email ?? 'ahmed@example.com',
    'phone' => '+970 599 123 456',
    'location' => 'Gaza, Palestine',
    'graduationYear' => '2024',
    'major' => 'Computer Science',
    'gpa' => '3.75',
    'bio' => 'Passionate software developer with experience in web development and mobile applications.',
    'skills' => ['JavaScript','React','Node.js','Python','TypeScript'],
  ];
  $initials = collect(explode(' ', $profile['name']))->map(fn($n)=>mb_substr($n,0,1))->join('');
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">My Profile</h1>

    <button id="btn-edit"
            data-testid="button-edit-profile"
            class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 transition inline-flex items-center">
      <i id="edit-icon" data-lucide="edit" class="h-4 w-4 mr-2"></i>
      <span id="edit-text">Edit Profile</span>
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="rounded-xl border border-border bg-card">
      <div class="p-6">
        <div class="flex flex-col items-center text-center">
          <div class="h-24 w-24 mb-4 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-2xl font-semibold">
            {{ $initials }}
          </div>

          <h2 class="text-xl font-semibold">{{ $profile['name'] }}</h2>
          <p class="text-muted-foreground">{{ $profile['major'] }}</p>

          <span class="mt-2 inline-flex items-center rounded-full bg-secondary px-3 py-1 text-xs text-secondary-foreground">
            Class of {{ $profile['graduationYear'] }}
          </span>

          <div class="w-full mt-6 space-y-3 text-sm">
            <div class="flex items-center gap-3">
              <i data-lucide="mail" class="h-4 w-4 text-muted-foreground"></i>
              <span>{{ $profile['email'] }}</span>
            </div>
            <div class="flex items-center gap-3">
              <i data-lucide="phone" class="h-4 w-4 text-muted-foreground"></i>
              <span>{{ $profile['phone'] }}</span>
            </div>
            <div class="flex items-center gap-3">
              <i data-lucide="map-pin" class="h-4 w-4 text-muted-foreground"></i>
              <span>{{ $profile['location'] }}</span>
            </div>
            <div class="flex items-center gap-3">
              <i data-lucide="graduation-cap" class="h-4 w-4 text-muted-foreground"></i>
              <span>ID: {{ $profile['academicId'] }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="lg:col-span-2 rounded-xl border border-border bg-card">
      <div class="p-6 border-b border-border">
        <div class="text-lg font-semibold">Personal Information</div>
        <div class="text-sm text-muted-foreground">Your profile details and bio</div>
      </div>

      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          @foreach([
            ['Full Name','name',$profile['name'],'input-name'],
            ['Email','email',$profile['email'],'input-email'],
            ['Phone','phone',$profile['phone'],'input-phone'],
            ['Location','location',$profile['location'],'input-location'],
            ['Major','major',$profile['major'],'input-major'],
            ['GPA','gpa',$profile['gpa'],'input-gpa'],
          ] as [$label,$name,$value,$test])
            <div class="space-y-2">
              <label class="text-sm font-medium">{{ $label }}</label>
              <input
                data-testid="{{ $test }}"
                data-editable
                disabled
                name="{{ $name }}"
                value="{{ $value }}"
                class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-80" />
            </div>
          @endforeach
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Bio</label>
          <textarea
            data-testid="input-bio"
            data-editable
            disabled
            class="min-h-24 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-80">{{ $profile['bio'] }}</textarea>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Skills</label>
          <div class="flex flex-wrap gap-2">
            @foreach($profile['skills'] as $skill)
              <span class="inline-flex items-center rounded-full border border-border px-3 py-1 text-xs">{{ $skill }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  let editing = false;
  const btn = document.getElementById('btn-edit');
  const icon = document.getElementById('edit-icon');
  const text = document.getElementById('edit-text');
  const fields = document.querySelectorAll('[data-editable]');

  btn.addEventListener('click', () => {
    editing = !editing;
    fields.forEach(el => el.toggleAttribute('disabled', !editing));


    btn.classList.toggle('bg-primary', editing);
    btn.classList.toggle('text-primary-foreground', editing);
    btn.classList.toggle('border-transparent', editing);

    text.textContent = editing ? 'Save Changes' : 'Edit Profile';
    icon.setAttribute('data-lucide', editing ? 'save' : 'edit');
    
    if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
  });
</script>
@endsection
