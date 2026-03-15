<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($title) ? $title . ' | PTC Alumni Tracking System' : 'PTC Alumni Tracking System' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}?v=2">
    <meta name="theme-color" content="{{ $appSettings->primary_color ?? '#2563eb' }}">


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


    <style>
      :root{
        --primary: {{ $appTheme['primary_hsl'] ?? '217 91% 60%' }};
        --ring: {{ $appTheme['primary_hsl'] ?? '217 91% 60%' }};
        --primary-foreground: 0 0% 100%;
      }
    </style>

    @include('partials.client-translations')
    @include('partials.asset-bundle')

    @stack('head')
</head>

<body class="min-h-screen bg-background text-foreground">

    @includeIf('partials.toasts')

    @yield('content')


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
