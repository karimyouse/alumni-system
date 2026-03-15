@extends('layouts.app')

@section('content')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center">

  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>

  <div class="absolute top-4 left-4 flex items-center gap-2 text-sm text-muted-foreground">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 hover:text-foreground transition">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      <span>Back</span>
    </a>
  </div>

  <div class="relative z-10 w-full max-w-md rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-primary text-primary-foreground flex items-center justify-center mb-3">
        <i data-lucide="lock" class="w-6 h-6"></i>
      </div>
      <h1 class="text-2xl font-bold">Reset Password</h1>
      <p class="text-sm text-muted-foreground">Create a new password for your account</p>
    </div>

    @if ($errors->any())
      <div class="mb-5 rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
        <div class="font-semibold text-destructive mb-1 inline-flex items-center gap-2">
          <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          Cannot reset
        </div>
        <div class="text-destructive/90">{{ $errors->first() }}</div>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">

      <div>
        <label class="text-sm font-medium">Email</label>
        <input value="{{ $email }}" disabled
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm opacity-80">
      </div>

      <div>
        <label class="text-sm font-medium">New Password</label>
        <input type="password" name="password" required
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
               placeholder="••••••••">
        @error('password') <div class="text-xs text-destructive mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium">Confirm Password</label>
        <input type="password" name="password_confirmation" required
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
               placeholder="••••••••">
      </div>

      <button type="submit"
        class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        Update Password
      </button>
    </form>
  </div>
</div>
@endsection
