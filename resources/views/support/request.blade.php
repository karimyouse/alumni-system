@extends('layouts.app')

@section('content')
@php
  $lastCode = $lastCode ?? '';
  $lastEmail = $lastEmail ?? '';

  $typedEmail = strtolower(trim((string) old('email', '')));
  $canShowLast = $lastCode && $lastEmail && $typedEmail && ($typedEmail === strtolower(trim((string)$lastEmail)));

  $maskedEmail = '';
  if ($lastEmail) {
    $parts = explode('@', $lastEmail);
    if (count($parts) === 2) {
      $name = $parts[0];
      $domain = $parts[1];
      $maskedEmail = (strlen($name) <= 2)
        ? ($name . '***@' . $domain)
        : (substr($name, 0, 2) . '***@' . $domain);
    }
  }
@endphp

@php($isRtl = app()->getLocale() === 'ar')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center">
  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>

  <div class="absolute top-4 left-4 app-top-back flex items-center gap-2 text-sm text-muted-foreground">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 hover:text-foreground transition">
      <i data-lucide="{{ $isRtl ? 'chevron-right' : 'chevron-left' }}" class="h-4 w-4"></i>
      <span>{{ __('Back to Login') }}</span>
    </a>
  </div>

  <div class="absolute top-4 right-4 app-top-actions flex items-center gap-2">
    @include('partials.language-dropdown')
    <button type="button"
            class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
            data-theme-toggle aria-label="{{ __('common.theme') }}">
      <i data-lucide="sun" class="h-4 w-4"></i>
    </button>
  </div>

  <div class="relative z-10 w-full max-w-lg rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-primary text-primary-foreground flex items-center justify-center mb-3">
        <i data-lucide="help-circle" class="w-6 h-6"></i>
      </div>
      <h1 class="text-2xl font-bold">{{ __('Contact Support') }}</h1>
      <p class="text-sm text-muted-foreground">{{ __('Send a request to the system administrator') }}</p>
    </div>

    @if($errors->any())
      <div class="mb-5 rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
        <div class="font-semibold text-destructive mb-1 inline-flex items-center gap-2">
          <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          {{ __('Cannot submit') }}
        </div>
        <div class="text-destructive/90">{{ $errors->first() }}</div>
      </div>
    @endif

    @if($lastCode)
      <div class="mb-5 rounded-xl border border-border bg-accent/10 p-4">
        <div class="text-sm font-semibold">{{ __('Saved on this device') }}</div>
        <div class="text-xs text-muted-foreground mt-1">
          {{ __('Tracking code:') }} <span class="font-mono text-foreground">{{ $lastCode }}</span>
        </div>

        @if($maskedEmail)
          <div class="text-xs text-muted-foreground">
            {{ __('Email:') }} <span class="text-foreground">{{ $maskedEmail }}</span>
          </div>
        @endif

        @if($canShowLast)
          <div class="mt-3 flex items-center justify-between gap-3">
            <div class="text-xs text-muted-foreground">
              {{ __('Email matches. You can open your last ticket.') }}
            </div>
            <a href="{{ route('support.track.show', ['code'=>$lastCode, 'email'=>$typedEmail]) }}"
               class="shrink-0 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
              {{ __('View') }}
            </a>
          </div>
        @else
          <div class="mt-3 text-xs text-muted-foreground">
            {{ __('To view it, enter your email in the form below, then you’ll be able to open the ticket.') }}
          </div>
        @endif
      </div>
    @endif

    <form method="POST" action="{{ route('support.request.store') }}" class="space-y-4">
      @csrf

      <input type="hidden" name="role" value="{{ old('role', $role ?? 'alumni') }}">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-medium">{{ __('Role') }}</label>
          <input value="{{ old('role', $role ?? 'alumni') }}" disabled
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm opacity-80">
        </div>

        <div>
          <label class="text-sm font-medium">{{ __('Identifier') }}</label>
          <input name="identifier" value="{{ old('identifier', $identifier ?? '') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="{{ __('Academic ID or Email') }}">
          @error('identifier') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-medium">{{ __('Your Name') }}</label>
          <input name="name" value="{{ old('name') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="{{ __('Full name') }}">
          @error('name') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-medium">{{ __('Email') }}</label>
          <input name="email" type="email" value="{{ old('email') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="you@example.com">
          @error('email') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div>
        <label class="text-sm font-medium">{{ __('Title') }}</label>
        <input name="title" value="{{ old('title', __('Account reactivation request')) }}" required
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">
        @error('title') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium">{{ __('Message') }}</label>
        <textarea name="message" rows="5" required
                  class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                  placeholder="{{ __('Explain your issue briefly...') }}">{{ old('message') }}</textarea>
        @error('message') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>

      <button type="submit"
              class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        {{ __('Send Request') }}
      </button>

      <div class="mt-4 text-center">
        <a href="{{ route('support.track.show') }}"
           class="text-sm text-muted-foreground hover:text-foreground underline underline-offset-4">
          {{ __('Already have a tracking code? Track your request') }}
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
