<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __($title ?? 'Dashboard') }} | PTC Alumni Tracking System</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}?v=2">
    <meta name="theme-color" content="{{ $appSettings->primary_color ?? '#2563eb' }}">

  <style>
    :root{
      --primary: {{ $appTheme['primary_hsl'] ?? '217 91% 60%' }};
      --ring: {{ $appTheme['primary_hsl'] ?? '217 91% 60%' }};
      --primary-foreground: 0 0% 100%;
      --dashboard-sidebar-width: 16rem;
      --dashboard-sidebar-collapsed-width: 5rem;
    }

    #dashboardSidebar {
      width: var(--dashboard-sidebar-width);
      will-change: transform, width;
    }

    #dashboardContent {
      min-width: 0;
      transition: padding 0.3s ease;
    }

    html[dir="ltr"] #dashboardSidebar {
      left: 0;
      right: auto;
    }

    html[dir="rtl"] #dashboardSidebar {
      right: 0;
      left: auto;
    }

    @media (min-width: 768px) {
      #dashboardSidebar {
        transform: translateX(0) !important;
      }

      html[dir="ltr"] #dashboardContent {
        padding-left: var(--dashboard-sidebar-width);
      }

      html[dir="rtl"] #dashboardContent {
        padding-right: var(--dashboard-sidebar-width);
      }

      body.dashboard-sidebar-collapsed #dashboardSidebar {
        width: var(--dashboard-sidebar-collapsed-width);
      }

      html[dir="ltr"] body.dashboard-sidebar-collapsed #dashboardContent {
        padding-left: var(--dashboard-sidebar-collapsed-width);
      }

      html[dir="rtl"] body.dashboard-sidebar-collapsed #dashboardContent {
        padding-right: var(--dashboard-sidebar-collapsed-width);
      }

      body.dashboard-sidebar-collapsed [data-sidebar-text],
      body.dashboard-sidebar-collapsed [data-sidebar-role],
      body.dashboard-sidebar-collapsed [data-sidebar-badge],
      body.dashboard-sidebar-collapsed [data-sidebar-user-meta],
      body.dashboard-sidebar-collapsed [data-sidebar-footer-label] {
        display: none !important;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-brand],
      body.dashboard-sidebar-collapsed [data-sidebar-item] {
        justify-content: center !important;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-item] > span {
        justify-content: center !important;
      }

      body.dashboard-sidebar-collapsed [data-sidebar-item] .sidebar-item-icon {
        margin-inline-end: 0 !important;
      }
    }

    @media (max-width: 767.98px) {
      #dashboardContent {
        padding-left: 0 !important;
        padding-right: 0 !important;
      }
    }

    body[data-dashboard-layout] {
      min-height: 100vh;
    }

    #dashboardContent {
      min-height: 100vh;
    }

    #dashboardContent > header {
      position: sticky;
      top: 0;
      z-index: 40;
    }

    #dashboardSidebar {
      overflow-y: auto;
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

  @include('partials.asset-bundle')
  @stack('head')
</head>

<body class="bg-background text-foreground" data-dashboard-layout>
  <script>
    (function () {
      try {
        if (
          window.matchMedia('(min-width: 768px)').matches &&
          localStorage.getItem('dashboard_sidebar_collapsed') === '1'
        ) {
          document.body.classList.add('dashboard-sidebar-collapsed');
        }
      } catch (e) {}
    })();
  </script>
  @includeIf('partials.toasts')

  @php
    $user = auth()->user();
    $userRole = $user?->role ? str_replace('_', ' ', $user->role) : '';
    $subLine = ($user?->role === 'alumni') ? ($user?->academic_id ?? '') : ($user?->email ?? '');
    $initials = $user?->name ? collect(explode(' ', $user->name))->map(fn($n)=>mb_substr($n,0,1))->join('') : 'U';
    $appName = __('app.brand');
    $isRtl = app()->getLocale() === 'ar';
    $roleLabels = [
      'alumni' => __('role.alumni'),
      'college' => __('role.college'),
      'company' => __('role.company'),
      'admin' => __('role.admin'),
      'super_admin' => __('role.super_admin'),
    ];

    $normalizedRole = strtolower(str_replace(' ', '_', (string) ($role ?? $userRole)));
    $displayRole = $roleLabels[$normalizedRole] ?? ($role ?? $userRole);

    $brandHref = match($user?->role) {
      'alumni' => '/alumni',
      'college' => '/college',
      'company' => '/company',
      'admin', 'super_admin' => '/admin',
      default => '/',
    };
  @endphp

  <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>

  <div class="min-h-screen w-full" data-dashboard-shell>

    <aside id="dashboardSidebar"
           class="fixed inset-y-0 {{ $isRtl ? 'right-0 border-l border-border text-right' : 'left-0 border-r border-border' }} z-50 flex w-64 flex-col bg-card h-screen flex-shrink-0 overflow-hidden {{ $isRtl ? 'translate-x-full md:translate-x-0' : '-translate-x-full md:translate-x-0' }} transition-all duration-300">

      <div class="border-b p-4 flex-shrink-0 flex items-center justify-between gap-2">
        <a href="{{ $brandHref }}"
           class="flex items-start gap-3 min-w-0 flex-1 {{ $isRtl ? 'flex-row-reverse text-right' : '' }}"
           data-sidebar-brand>
          <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-primary text-primary-foreground flex-shrink-0">
            <i data-lucide="graduation-cap" class="h-5 w-5"></i>
          </div>

          <div class="flex flex-col justify-center min-w-0 flex-1 {{ $isRtl ? 'items-end text-right' : 'items-start text-left' }}"
               data-sidebar-text>
            <span class="block w-full font-semibold text-sm leading-tight {{ $isRtl ? 'text-right' : 'text-left' }}">
              {{ $appName }}
            </span>
            <span class="block w-full text-xs text-muted-foreground leading-tight mt-1 {{ $isRtl ? 'text-right' : 'text-left' }}"
                  data-sidebar-role>
              {{ $displayRole }}
            </span>
          </div>
        </a>

        <button type="button"
                class="md:hidden h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
                data-mobile-sidebar-close
                aria-label="{{ __('common.close') }}">
          <i data-lucide="x" class="h-4 w-4"></i>
        </button>
      </div>

      <nav class="flex-1 p-3 space-y-1 overflow-y-auto {{ $isRtl ? 'text-right' : '' }}">
        @foreach(($nav ?? []) as $item)
          @php
            $hrefPath = ltrim($item['href'], '/');
            $active = request()->is($hrefPath) || request()->is($hrefPath.'/*');
            $badge = (int) ($item['badge'] ?? 0);
          @endphp

          <a href="{{ $item['href'] }}"
             data-sidebar-item
             class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition
             {{ $isRtl ? 'flex-row-reverse text-right' : '' }}
             {{ $active ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
            <span class="flex items-center gap-3 min-w-0 flex-1 {{ $isRtl ? 'flex-row-reverse text-right justify-start' : '' }}">
              <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="h-4 w-4 sidebar-item-icon flex-shrink-0"></i>
              <span class="truncate" data-sidebar-text>{{ $item['label'] }}</span>
            </span>

            @if(!$active && $badge > 0)
              <span data-sidebar-badge
                    class="{{ $isRtl ? 'mr-auto' : 'ml-auto' }} inline-flex min-w-[22px] h-[22px] items-center justify-center rounded-full bg-primary/15 px-2 text-[11px] font-medium text-primary flex-shrink-0">
                {{ $badge > 9 ? '9+' : $badge }}
              </span>
            @endif
          </a>
        @endforeach
      </nav>

      <div class="border-t p-4 flex-shrink-0">
  <div class="flex items-center gap-2 mb-4 w-full {{ $isRtl ? 'flex-row-reverse text-right' : '' }}">
    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-semibold flex-shrink-0">
      {{ $initials }}
    </div>

    <div class="flex-1 min-w-0 w-full {{ $isRtl ? 'text-right items-end' : 'text-left items-start' }} flex flex-col"
         data-sidebar-user-meta>
      <p class="text-sm font-medium truncate w-full {{ $isRtl ? 'text-right' : 'text-left' }}">{{ $user?->name }}</p>
      <p class="text-xs text-muted-foreground truncate w-full {{ $isRtl ? 'text-right' : 'text-left' }}">{{ $subLine }}</p>
    </div>
  </div>

  <form method="POST" action="/logout" class="w-full">
    @csrf
    <button type="submit"
            data-sidebar-item
            class="w-full inline-flex items-center rounded-md px-3 py-2 text-sm hover:bg-accent/50 transition {{ $isRtl ? 'flex-row-reverse justify-start text-right gap-2' : 'justify-start gap-2' }}">
      <i data-lucide="log-out" class="h-4 w-4 sidebar-item-icon flex-shrink-0"></i>
      <span class="w-full {{ $isRtl ? 'text-right' : 'text-left' }}" data-sidebar-footer-label>{{ __('nav.logout') }}</span>
    </button>
  </form>
</div>

    </aside>

    <div id="dashboardContent" class="min-h-screen flex flex-col">

      <header class="flex-shrink-0 z-40 flex items-center justify-between gap-4 border-b bg-background px-4 h-14">
        <div class="flex items-center gap-2">
          <button type="button"
                  class="md:hidden h-9 w-9 inline-flex items-center justify-center rounded-md border border-border"
                  data-mobile-sidebar-open
                  aria-label="{{ __('common.open_menu') }}">
            <i data-lucide="panel-left" class="h-4 w-4"></i>
          </button>

          <button type="button"
                  class="hidden md:inline-flex h-9 w-9 items-center justify-center rounded-md border border-border hover:bg-accent/50"
                  data-sidebar-toggle
                  aria-label="{{ __('common.toggle_sidebar') }}">
            <i data-lucide="panel-left" class="h-4 w-4"></i>
          </button>

          <h1 class="text-lg font-semibold">{{ $title ?? '' }}</h1>
        </div>

        <div class="flex items-center gap-2">
          @includeIf('partials.notifications-dropdown')

          @include('partials.language-dropdown', [
            'buttonClass' => 'h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50',
            'buttonLabel' => __('common.language'),
            'menuWidth' => 'w-40',
            'menuAlignClass' => $isRtl ? 'left-0 origin-top-left' : 'right-0 origin-top-right',
            'menuTextAlignClass' => $isRtl ? 'text-right' : 'text-left',
          ])

          <button class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
                  data-theme-toggle aria-label="{{ __('common.theme') }}" type="button">
            <i data-lucide="moon" class="h-4 w-4"></i>
          </button>
        </div>
      </header>

      <main class="flex-1 p-4 md:p-6">
        @yield('content')
      </main>
    </div>

  </div>

  <script>
    (function () {
      function setupLanguageDropdowns() {
        document.querySelectorAll('[data-lang-dropdown]').forEach(function (wrap) {
          if (wrap.dataset.langDropdownReady === '1') return;
          wrap.dataset.langDropdownReady = '1';

          var toggle = wrap.querySelector('[data-lang-toggle]');
          var menu = wrap.querySelector('[data-lang-menu]');

          if (!toggle || !menu) return;

          function syncRedirectFields() {
            var redirectTo = window.location.pathname + window.location.search;
            var fragment = window.location.hash ? window.location.hash.substring(1) : '';

            wrap.querySelectorAll('[data-lang-redirect]').forEach(function (input) {
              input.value = redirectTo + window.location.hash;
            });

            wrap.querySelectorAll('[data-lang-fragment]').forEach(function (input) {
              input.value = fragment;
            });
          }

          function closeMenu() {
            menu.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
          }

          function openMenu() {
            syncRedirectFields();
            menu.classList.remove('hidden');
            toggle.setAttribute('aria-expanded', 'true');
          }

          toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var isHidden = menu.classList.contains('hidden');
            document.querySelectorAll('[data-lang-menu]').forEach(function (otherMenu) {
              if (otherMenu !== menu) {
                otherMenu.classList.add('hidden');
              }
            });
            document.querySelectorAll('[data-lang-toggle]').forEach(function (otherToggle) {
              if (otherToggle !== toggle) {
                otherToggle.setAttribute('aria-expanded', 'false');
              }
            });

            if (isHidden) {
              openMenu();
            } else {
              closeMenu();
            }
          });

          wrap.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
              syncRedirectFields();
            });
          });

          document.addEventListener('click', function (event) {
            if (!wrap.contains(event.target)) {
              closeMenu();
            }
          });

          document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
              closeMenu();
            }
          });
        });
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupLanguageDropdowns);
      } else {
        setupLanguageDropdowns();
      }
    })();
  </script>

  @stack('scripts')
</body>
</html>
