@extends('layouts.app')

@section('content')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center">


  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>


  <div class="absolute top-4 left-4 flex items-center gap-2 text-sm text-muted-foreground">
    <a href="/login" class="inline-flex items-center gap-2 hover:text-foreground transition">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      <span>Login</span>
    </a>
  </div>

  <div class="absolute top-4 right-4 flex items-center gap-2">
    <button type="button"
            class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
            aria-label="Language">
      <i data-lucide="globe" class="h-4 w-4"></i>
    </button>

    <button type="button"
            class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
            data-theme-toggle aria-label="Theme">
      <i data-lucide="sun" class="h-4 w-4"></i>
    </button>
  </div>


  <div class="relative z-10 w-full max-w-md rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-purple-500/15 text-purple-400 flex items-center justify-center mb-3">
        <i data-lucide="building-2" class="w-6 h-6"></i>
      </div>

      <h1 class="text-xl font-bold">Company Registration</h1>
      <p class="text-sm text-muted-foreground">Create your company account</p>
    </div>

    <form method="POST" action="#" class="space-y-4">
      @csrf


      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-medium">Company Name <span class="text-destructive">*</span></label>
          <input type="text"
                 placeholder="Acme Corporation"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>

        <div>
          <label class="text-xs font-medium">Contact Person Name <span class="text-destructive">*</span></label>
          <input type="text"
                 placeholder="John Doe"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>
      </div>


      <div>
        <label class="text-xs font-medium">Email Address <span class="text-destructive">*</span></label>
        <input type="email"
               placeholder="contact@company.com"
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
      </div>


      <div>
        <label class="text-xs font-medium">Password <span class="text-destructive">*</span></label>
        <input type="password"
               placeholder="••••••••"
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
      </div>


      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-medium">Industry</label>
          <input type="text"
                 placeholder="Technology"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>

        <div>
          <label class="text-xs font-medium">Location</label>
          <input type="text"
                 placeholder="Gaza, Palestine"
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
        </div>
      </div>


      <div>
        <label class="text-xs font-medium">Website</label>
        <input type="url"
               placeholder="https://company.com"
               class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring" />
      </div>

    
      <div>
        <label class="text-xs font-medium">Company Description</label>
        <textarea rows="4"
                  placeholder="Tell us about your company..."
                  class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"></textarea>
      </div>

      <button type="submit"
              class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        Register
      </button>

      <div class="text-center text-xs text-muted-foreground pt-2">
        Already have an account?
        <a href="/login" class="text-primary hover:underline">Sign In</a>
      </div>
    </form>
  </div>
</div>
@endsection
