@php
  $dashboard = route('login');

  if (auth()->check()) {
    $role = auth()->user()->role ?? null;

    $dashboard = match ($role) {
      'alumni' => '/alumni',
      'college' => '/college',
      'company' => '/company',
      'admin', 'super_admin' => '/admin',
      default => route('login'),
    };
  }
@endphp

<header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="max-w-7xl mx-auto flex h-16 items-center justify-between gap-4 px-4 md:px-6">
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-primary-foreground">
        <i data-lucide="graduation-cap" class="h-6 w-6"></i>
      </div>
      <span class="font-semibold text-lg hidden sm:inline-block">Alumni System</span>
    </a>

    <nav class="hidden md:flex items-center gap-6">
      <a href="{{ route('home') }}" class="text-sm font-medium hover:text-primary transition-colors" data-testid="link-home">
        {{ __('nav.home') }}
      </a>
      <a href="{{ route('home') }}#about" class="text-sm font-medium hover:text-primary transition-colors" data-testid="link-about">
        {{ __('nav.about') }}
      </a>
      <a href="{{ route('home') }}#contact" class="text-sm font-medium hover:text-primary transition-colors" data-testid="link-contact">
        {{ __('nav.contact') }}
      </a>
    </nav>

    <div class="flex items-center gap-2">
      @include('partials.language-dropdown', ['buttonClass' => 'h-10 w-10 inline-flex items-center justify-center rounded-md hover:bg-accent/50 transition'])

      <x-ui.button variant="ghost" size="icon" data-testid="button-theme-toggle" data-theme-toggle type="button">
        <i data-lucide="moon" class="h-4 w-4"></i>
      </x-ui.button>

      @if(auth()->check())
        <div class="flex items-center gap-2">
          <a href="{{ $dashboard }}">
            <x-ui.button variant="ghost" size="sm" data-testid="button-dashboard">
              {{ __('nav.dashboard') }}
            </x-ui.button>
          </a>

          <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <x-ui.button variant="outline" size="sm" type="submit" data-testid="button-logout">
              {{ __('nav.logout') }}
            </x-ui.button>
          </form>
        </div>
      @else
        <a href="{{ route('login') }}">
          <x-ui.button size="sm" data-testid="button-login">
            {{ __('nav.login') }}
          </x-ui.button>
        </a>
      @endif
    </div>
  </div>
</header>
