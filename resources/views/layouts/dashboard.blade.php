<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Dashboard' }}</title>

  <style>
    :root{
      --primary: {{ $appTheme['primary_hsl'] ?? '217 91% 60%' }};
      --ring: {{ $appTheme['primary_hsl'] ?? '217 91% 60%' }};
      --primary-foreground: 0 0% 100%;
    }

    @media (min-width: 768px) {
      body.dashboard-sidebar-collapsed #dashboardSidebar {
        width: 5rem;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-text],
      body.dashboard-sidebar-collapsed [data-sidebar-role],
      body.dashboard-sidebar-collapsed [data-sidebar-badge],
      body.dashboard-sidebar-collapsed [data-sidebar-user-meta],
      body.dashboard-sidebar-collapsed [data-sidebar-footer-label] {
        display: none !important;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-brand] {
        justify-content: center;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-item] {
        justify-content: center;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-item] .sidebar-item-icon {
        margin-inline-end: 0 !important;
      }
    }
  </style>

  <script>
    (function () {
      try {
        const saved = localStorage.getItem('theme');
        const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (saved === 'dark' || (!saved && systemDark)) {
          document.documentElement.classList.add('dark');
        }
      } catch (e) {}
    })();
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-background text-foreground" data-dashboard-layout>
  @includeIf('partials.toasts')

  @php
    $user = auth()->user();
    $userRole = $user?->role ? str_replace('_', ' ', $user->role) : '';
    $subLine = ($user?->role === 'alumni') ? ($user?->academic_id ?? '') : ($user?->email ?? '');
    $initials = $user?->name ? collect(explode(' ', $user->name))->map(fn($n)=>mb_substr($n,0,1))->join('') : 'U';
    $appName = 'Alumni System';

    $brandHref = match($user?->role) {
      'alumni' => '/alumni',
      'college' => '/college',
      'company' => '/company',
      'admin', 'super_admin' => '/admin',
      default => '/',
    };
  @endphp

  <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>

  <div class="h-screen w-full flex overflow-hidden">

    <aside id="dashboardSidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-border bg-card
                  h-screen flex-shrink-0 overflow-hidden -translate-x-full transition-all duration-300
                  md:static md:translate-x-0">

      <div class="border-b p-4 flex-shrink-0 flex items-center justify-between gap-2">
        <a href="{{ $brandHref }}" class="flex items-center gap-3 min-w-0" data-sidebar-brand>
          <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-primary text-primary-foreground flex-shrink-0">
            <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          </div>
          <div class="flex flex-col min-w-0" data-sidebar-text>
            <span class="font-semibold text-sm truncate">{{ $appName }}</span>
            <span class="text-xs text-muted-foreground capitalize" data-sidebar-role>{{ $role ?? $userRole }}</span>
          </div>
        </a>

        <button type="button"
                class="md:hidden h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
                data-mobile-sidebar-close
                aria-label="Close menu">
          <i data-lucide="x" class="h-4 w-4"></i>
        </button>
      </div>

      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        @foreach(($nav ?? []) as $item)
          @php
            $hrefPath = ltrim($item['href'], '/');
            $active = request()->is($hrefPath) || request()->is($hrefPath.'/*');
          @endphp

          <a href="{{ $item['href'] }}"
             data-sidebar-item
             class="flex items-center justify-between rounded-md px-3 py-2 text-sm transition
             {{ $active ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
            <span class="flex items-center gap-3 min-w-0">
              <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="h-4 w-4 sidebar-item-icon flex-shrink-0"></i>
              <span class="truncate" data-sidebar-text>{{ $item['label'] }}</span>
            </span>

            @if(isset($item['badge']) && $item['badge'] > 0)
              <span data-sidebar-badge
                    class="inline-flex items-center justify-center rounded-full bg-secondary px-2 py-0.5 text-[11px] text-secondary-foreground">
                {{ $item['badge'] }}
              </span>
            @endif
          </a>
        @endforeach
      </nav>

      <div class="border-t p-4 flex-shrink-0">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-semibold flex-shrink-0">
            {{ $initials }}
          </div>
          <div class="flex-1 min-w-0" data-sidebar-user-meta>
            <p class="text-sm font-medium truncate">{{ $user?->name }}</p>
            <p class="text-xs text-muted-foreground truncate">{{ $subLine }}</p>
          </div>
        </div>

        <form method="POST" action="/logout">
          @csrf
          <button type="submit"
                  data-sidebar-item
                  class="w-full justify-start gap-2 inline-flex items-center rounded-md px-3 py-2 text-sm hover:bg-accent/50 transition">
            <i data-lucide="log-out" class="h-4 w-4 sidebar-item-icon"></i>
            <span data-sidebar-footer-label>Logout</span>
          </button>
        </form>
      </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

      <header class="flex-shrink-0 z-40 flex items-center justify-between gap-4 border-b bg-background px-4 h-14">
        <div class="flex items-center gap-2">
          <button type="button"
                  class="md:hidden h-9 w-9 inline-flex items-center justify-center rounded-md border border-border"
                  data-mobile-sidebar-open
                  aria-label="Open menu">
            <i data-lucide="panel-left" class="h-4 w-4"></i>
          </button>

          <button type="button"
                  class="hidden md:inline-flex h-9 w-9 items-center justify-center rounded-md border border-border hover:bg-accent/50"
                  data-sidebar-toggle
                  aria-label="Toggle sidebar">
            <i data-lucide="panel-left" class="h-4 w-4"></i>
          </button>

          <h1 class="text-lg font-semibold">{{ $title ?? '' }}</h1>
        </div>

        <div class="flex items-center gap-2">
          @includeIf('partials.notifications-dropdown')

          <button class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50" aria-label="Language" type="button">
            <i data-lucide="globe" class="h-4 w-4"></i>
          </button>

          <button class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
                  data-theme-toggle aria-label="Theme" type="button">
            <i data-lucide="moon" class="h-4 w-4"></i>
          </button>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-4 md:p-6">
        @yield('content')
      </main>
    </div>

  </div>
</body>
</html>
