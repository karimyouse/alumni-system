@extends('layouts.app')

@php
  $adminDefaultHex = '#f59e0b';
  $currentAdminHex = strtolower($appSettings->primary_color ?? '#2563eb');

  $useCustomHomeTheme = $currentAdminHex !== strtolower($adminDefaultHex);

  $homePrimaryHsl = $useCustomHomeTheme
      ? ($appTheme['primary_hsl'] ?? '217 91% 60%')
      : '217 91% 60%';
@endphp

@push('head')
<style>
  :root{
    --primary: {{ $homePrimaryHsl }} !important;
    --ring: {{ $homePrimaryHsl }} !important;
    --primary-foreground: 0 0% 100% !important;
  }
</style>
@endpush

@section('content')
@php
  $institutionName = __($appSettings->institution_name ?? 'Palestine Technical College');

  $stats = [
    ['value' => number_format((int)($homeStats['alumni'] ?? 0)) . '+', 'label' => __('home.stats.alumni')],
    ['value' => number_format((int)($homeStats['jobs'] ?? 0)) . '+', 'label' => __('home.stats.jobs')],
    ['value' => number_format((int)($homeStats['workshops'] ?? 0)) . '+', 'label' => __('home.stats.workshops')],
    ['value' => number_format((int)($homeStats['companies'] ?? 0)) . '+', 'label' => __('home.stats.companies')],
  ];

  $whyMatters = [
    [
      'icon' => 'target',
      'title' => __('home.about.problem.title'),
      'description' => __('home.about.problem.description'),
    ],
    [
      'icon' => 'handshake',
      'title' => __('home.about.solution.title'),
      'description' => __('home.about.solution.description'),
    ],
    [
      'icon' => 'trending-up',
      'title' => __('home.about.impact.title'),
      'description' => __('home.about.impact.description'),
    ],
  ];

  $portals = [
    [
      'icon' => 'graduation-cap',
      'title' => __('home.portals.alumni.title'),
      'description' => __('home.portals.alumni.description'),
      'color' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
    ],
    [
      'icon' => 'building-2',
      'title' => __('home.portals.college.title'),
      'description' => __('home.portals.college.description'),
      'color' => 'bg-green-500/10 text-green-600 dark:text-green-400',
    ],
    [
      'icon' => 'briefcase',
      'title' => __('home.portals.company.title'),
      'description' => __('home.portals.company.description'),
      'color' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400',
    ],
    [
      'icon' => 'shield-check',
      'title' => __('home.portals.admin.title'),
      'description' => __('home.portals.admin.description'),
      'color' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400',
    ],
  ];

  $features = [
    ['icon' => 'users', 'title' => __('home.features.profiles'), 'desc' => __('home.features.profiles.desc')],
    ['icon' => 'briefcase', 'title' => __('home.features.jobs'), 'desc' => __('home.features.jobs.desc')],
    ['icon' => 'calendar-days', 'title' => __('home.features.workshops'), 'desc' => __('home.features.workshops.desc')],
    ['icon' => 'bell', 'title' => __('home.features.notifications'), 'desc' => __('home.features.notifications.desc')],
    ['icon' => 'trophy', 'title' => __('home.features.leaderboard'), 'desc' => __('home.features.leaderboard.desc')],
    ['icon' => 'user-plus', 'title' => __('home.features.recommendations'), 'desc' => __('home.features.recommendations.desc')],
  ];
@endphp

<div class="min-h-screen flex flex-col">
  <x-common.header />

  <main class="flex-1">
    <section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden" data-testid="section-hero">
      <div class="absolute inset-0 bg-gradient-to-br from-primary/30 via-primary/10 to-accent/20"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/50 to-transparent"></div>
      <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMyMTIxMjEiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50"></div>

      <div class="relative z-10 max-w-5xl mx-auto px-4 md:px-6 text-center py-20">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-medium mb-6">
          <i data-lucide="graduation-cap" class="h-4 w-4"></i>
          {{ $institutionName }}
        </div>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-6" data-testid="text-hero-title">
          {{ __('home.hero.title') }}
        </h1>

        <p class="text-xl md:text-2xl text-muted-foreground mb-4">
          {{ __('home.hero.subtitle') }}
        </p>

        <p class="text-base md:text-lg text-muted-foreground max-w-2xl mx-auto mb-10">
          {{ __('home.hero.description') }}
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <a href="{{ route('login') }}">
            <x-ui.button size="lg" class="gap-2" data-testid="button-hero-login">
              {{ __('home.hero.login') }}
              <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </x-ui.button>
          </a>

          <a href="#about">
            <x-ui.button size="lg" variant="outline" data-testid="button-hero-learn-more">
              {{ __('home.hero.learnMore') }}
            </x-ui.button>
          </a>
        </div>
      </div>
    </section>

    <section id="about" class="py-20 bg-card reveal-section" data-testid="section-about">
      <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="text-center mb-12 reveal-item">
          <h2 class="text-3xl md:text-4xl font-bold mb-4" data-testid="text-about-title">
            {{ __('home.about.title') }}
          </h2>
          <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
            {{ __('home.about.description') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
          @foreach($whyMatters as $item)
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm text-center reveal-item" style="transition-delay: {{ $loop->index * 90 }}ms;">
              <div class="flex flex-col space-y-1.5 p-6">
                <div class="mx-auto w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-2">
                  <i data-lucide="{{ $item['icon'] }}" class="h-6 w-6 text-primary"></i>
                </div>
                <div class="text-lg font-semibold leading-none tracking-tight">{{ $item['title'] }}</div>
              </div>
              <div class="p-6 pt-0">
                <p class="text-sm text-muted-foreground">{{ $item['description'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="py-20 reveal-section" data-testid="section-portals">
      <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="text-center mb-12 reveal-item">
          <h2 class="text-3xl md:text-4xl font-bold mb-4" data-testid="text-portals-title">
            {{ __('home.portals.title') }}
          </h2>
          <p class="text-lg text-muted-foreground">
            {{ __('home.portals.subtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          @foreach($portals as $portal)
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm hover-elevate transition-all duration-300 reveal-item" style="transition-delay: {{ $loop->index * 90 }}ms;">
              <div class="flex flex-col space-y-1.5 p-6">
                <div class="w-12 h-12 rounded-lg {{ $portal['color'] }} flex items-center justify-center mb-2">
                  <i data-lucide="{{ $portal['icon'] }}" class="h-6 w-6"></i>
                </div>
                <div class="text-lg font-semibold leading-none tracking-tight">{{ $portal['title'] }}</div>
              </div>
              <div class="p-6 pt-0">
                <div class="text-sm text-muted-foreground">{{ $portal['description'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="py-20 bg-card reveal-section" data-testid="section-features">
      <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="text-center mb-12 reveal-item">
          <h2 class="text-3xl md:text-4xl font-bold mb-4" data-testid="text-features-title">
            {{ __('home.features.title') }}
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($features as $feature)
            <div class="flex gap-4 p-6 rounded-lg bg-background border hover-elevate transition-all duration-300 reveal-item" style="transition-delay: {{ $loop->index * 90 }}ms;">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <i data-lucide="{{ $feature['icon'] }}" class="h-5 w-5 text-primary"></i>
              </div>
              <div>
                <h3 class="font-semibold mb-1">{{ $feature['title'] }}</h3>
                <p class="text-sm text-muted-foreground">{{ $feature['desc'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="py-16 bg-primary text-primary-foreground reveal-section reveal-section--light" data-testid="section-stats">
      <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          @foreach($stats as $i => $stat)
            <div class="text-center reveal-item" style="transition-delay: {{ $loop->index * 100 }}ms;">
              <div class="text-4xl md:text-5xl font-bold mb-2" data-testid="text-stat-{{ $i }}">
                {{ $stat['value'] }}
              </div>
              <div class="text-sm md:text-base opacity-90">{{ $stat['label'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="py-20 reveal-section" data-testid="section-cta">
      <div class="max-w-4xl mx-auto px-4 md:px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4 reveal-item">
          {{ __('home.cta.title') }}
        </h2>
        <p class="text-lg text-muted-foreground mb-8 reveal-item" style="transition-delay: 90ms;">
          {{ __('home.cta.description') }}
        </p>
        <a href="{{ route('login') }}" class="reveal-item inline-flex" style="transition-delay: 180ms;">
          <x-ui.button size="lg" class="gap-2" data-testid="button-cta-login">
            {{ __('home.hero.login') }}
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
          </x-ui.button>
        </a>
      </div>
    </section>
  </main>

  <x-common.footer />
</div>
@endsection
