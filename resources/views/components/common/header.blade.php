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

  $isRtl = app()->getLocale() === 'ar';
  $menuAlign = $isRtl ? 'left-0' : 'right-0';
  $menuTextAlign = $isRtl ? 'text-right' : 'text-left';
@endphp

<header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="max-w-7xl mx-auto flex h-16 items-center justify-between gap-4 px-4 md:px-6">
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-primary-foreground">
        <i data-lucide="graduation-cap" class="h-6 w-6"></i>
      </div>
      <span class="font-semibold text-lg hidden sm:inline-block">{{ __('app.brand') }}</span>
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

      @if(\Illuminate\Support\Facades\Route::has('support.request.show'))
        <a href="{{ route('support.request.show') }}"
           class="text-sm font-medium hover:text-primary transition-colors"
           data-testid="link-support">
          {{ __('nav.help') }}
        </a>
      @endif
    </nav>

    <div class="flex items-center gap-2">
      <div class="relative" id="langWrap">
        <x-ui.button variant="ghost" size="icon" data-testid="button-language-toggle" id="langBtn" type="button" aria-label="{{ __('common.language') }}">
          <i data-lucide="globe" class="h-4 w-4"></i>
        </x-ui.button>

        <div id="langMenu"
             class="hidden absolute {{ $menuAlign }} mt-2 w-40 rounded-md border border-border bg-popover text-popover-foreground shadow-lg z-50">
          <a href="{{ route('lang.switch', ['locale' => 'en']) }}"
             class="block w-full {{ $menuTextAlign }} px-3 py-2 text-sm hover:bg-accent rounded-md {{ app()->getLocale()==='en' ? 'bg-accent' : '' }}"
             data-testid="menu-item-english"
             data-lang-link
             data-locale="en">
            {{ __('lang.english') }}
          </a>

          <a href="{{ route('lang.switch', ['locale' => 'ar']) }}"
             class="block w-full {{ $menuTextAlign }} px-3 py-2 text-sm hover:bg-accent rounded-md {{ app()->getLocale()==='ar' ? 'bg-accent' : '' }}"
             data-testid="menu-item-arabic"
             data-lang-link
             data-locale="ar">
            {{ __('lang.arabic') }}
          </a>
        </div>
      </div>

      <x-ui.button variant="ghost" size="icon" data-testid="button-theme-toggle" data-theme-toggle type="button" aria-label="{{ __('common.theme') }}">
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

<script>
  (function () {
    const wrap = document.getElementById('langWrap');
    const btn  = document.getElementById('langBtn');
    const menu = document.getElementById('langMenu');

    if (!wrap || !btn || !menu) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });

    document.addEventListener('click', function (e) {
      if (!wrap.contains(e.target)) menu.classList.add('hidden');
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') menu.classList.add('hidden');
    });
  })();
</script>
