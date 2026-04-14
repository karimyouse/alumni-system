@extends('layouts.dashboard')

@php
  $title = __('Company Profile');
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>$applicationBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
  ];
@endphp

@section('content')
<div class="max-w-4xl space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold leading-tight">Company Profile</h1>
      <p class="text-sm text-muted-foreground">
        These details appear to alumni before they apply or register.
      </p>
    </div>

    <a href="{{ route('company.dashboard') }}"
       class="inline-flex w-full items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 sm:w-auto">
      Back
    </a>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive">
      <div class="font-semibold">Please fix the highlighted fields.</div>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
    <form method="POST" action="{{ route('company.profile.update') }}"
          class="rounded-xl border border-border bg-card p-4 space-y-4 sm:p-6">
      @csrf

      <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="text-sm font-medium">Company Name <span class="text-destructive">*</span></label>
          <input type="text" name="company_name"
                 value="{{ old('company_name', $companyProfile?->company_name ?? $companyName) }}"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>

        <div>
          <label class="text-sm font-medium">Industry</label>
          <input type="text" name="industry"
                 value="{{ old('industry', $companyProfile?->industry) }}"
                 placeholder="Technology, Training, Design..."
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>

        <div>
          <label class="text-sm font-medium">Location</label>
          <input type="text" name="location"
                 value="{{ old('location', $companyProfile?->location) }}"
                 placeholder="Gaza, Ramallah, Remote..."
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>

        <div>
          <label class="text-sm font-medium">Website</label>
          <input type="text" name="website"
                 value="{{ old('website', $companyProfile?->website) }}"
                 placeholder="https://company.com"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>

        <div>
          <label class="text-sm font-medium">Contact Person</label>
          <input type="text" name="contact_person_name"
                 value="{{ old('contact_person_name', $companyProfile?->contact_person_name) }}"
                 placeholder="Hiring manager name"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>
      </div>

      <div>
        <label class="text-sm font-medium">Company Description</label>
        <textarea name="description" rows="6"
                  placeholder="Tell alumni what your company does, who you serve, and why this opportunity is trustworthy."
                  class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">{{ old('description', $companyProfile?->description) }}</textarea>
      </div>

      <button type="submit"
              class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 sm:w-auto">
        Save Profile
      </button>
    </form>

    <div class="space-y-4">
      @include('partials.company-trust-card', [
        'company' => auth()->user(),
        'profile' => $companyProfile,
        'fallbackName' => $companyName,
      ])

      <div class="rounded-xl border border-border bg-card p-4 text-sm text-muted-foreground">
        <div class="font-semibold text-foreground">Trust checklist</div>
        <div class="mt-3 space-y-2">
          <div class="flex items-center gap-2">
            <i data-lucide="{{ $companyProfile?->industry ? 'check-circle-2' : 'circle' }}" class="h-4 w-4 text-primary"></i>
            Industry added
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="{{ $companyProfile?->website ? 'check-circle-2' : 'circle' }}" class="h-4 w-4 text-primary"></i>
            Website added
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="{{ $companyProfile?->description ? 'check-circle-2' : 'circle' }}" class="h-4 w-4 text-primary"></i>
            Description added
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
