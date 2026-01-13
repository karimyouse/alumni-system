@php

  $dashboard = '/login';
  if(auth()->check()){
    $role = auth()->user()->role ?? null;
    $dashboard = match($role){
      'alumni' => '/alumni',
      'college' => '/college',
      'company' => '/company',
      'super_admin' => '/admin',
      default => '/login',
    };
  }
@endphp

<header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="max-w-7xl mx-auto flex h-16 items-center justify-between gap-4 px-4 md:px-6">
    <a href="/" class="flex items-center gap-2">
      <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-primary-foreground">
        <i data-lucide="graduation-cap" class="h-6 w-6"></i>
      </div>
      <span class="font-semibold text-lg hidden sm:inline-block">Alumni System</span>
    </a>

    <nav class="hidden md:flex items-center gap-6">
      <a href="/" class="text-sm font-medium hover:text-primary transition-colors" data-testid="link-home">
        {{ __('nav.home') }}
      </a>
      <a href="/#about" class="text-sm font-medium hover:text-primary transition-colors" data-testid="link-about">
        {{ __('nav.about') }}
      </a>
      <a href="/#contact" class="text-sm font-medium hover:text-primary transition-colors" data-testid="link-contact">
        {{ __('nav.contact') }}
      </a>
    </nav>

    <div class="flex items-center gap-2">

      <div class="relative">
        <x-ui.button variant="ghost" size="icon" data-testid="button-language-toggle" data-lang-toggle>
          <i data-lucide="globe" class="h-4 w-4"></i>
        </x-ui.button>

        <div data-lang-menu class="hidden absolute right-0 mt-2 w-40 rounded-md border border-border bg-popover text-popover-foreground shadow-lg">
          <form method="POST" action="/lang" class="p-1">
            @csrf
            <input type="hidden" name="locale" value="en">
            <button type="submit" class="w-full text-left px-3 py-2 text-sm rounded-md hover:bg-accent {{ app()->getLocale()==='en' ? 'bg-accent' : '' }}" data-testid="menu-item-english">
              English
            </button>
          </form>
          <form method="POST" action="/lang" class="p-1 pt-0">
            @csrf
            <input type="hidden" name="locale" value="ar">
            <button type="submit" class="w-full text-left px-3 py-2 text-sm rounded-md hover:bg-accent {{ app()->getLocale()==='ar' ? 'bg-accent' : '' }}" data-testid="menu-item-arabic">
              العربية
            </button>
          </form>
        </div>
      </div>


      <x-ui.button variant="ghost" size="icon" data-testid="button-theme-toggle" data-theme-toggle>

        <i data-lucide="moon" class="h-4 w-4"></i>
      </x-ui.button>

      @if(auth()->check())
        <div class="flex items-center gap-2">
          <a href="{{ $dashboard }}">
            <x-ui.button variant="ghost" size="sm" data-testid="button-dashboard">
              {{ __('nav.dashboard') }}
            </x-ui.button>
          </a>

          
          <form method="POST" action="/logout">
            @csrf
            <x-ui.button variant="outline" size="sm" type="submit" data-testid="button-logout">
              {{ __('nav.logout') }}
            </x-ui.button>
          </form>
        </div>
      @else
        <a href="/login">
          <x-ui.button size="sm" data-testid="button-login">
            {{ __('nav.login') }}
          </x-ui.button>
        </a>
      @endif
    </div>
  </div>
</header>
