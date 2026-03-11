<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __($title ?? 'Alumni Tracking System') }}</title>


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

    @includeIf('partials.client-translations')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ Optional extra head from pages --}}
    @stack('head')
</head>

<body class="min-h-screen bg-background text-foreground">

    {{-- ✅ Global toasts (single source) --}}
    @includeIf('partials.toasts')

    @yield('content')

    {{-- ✅ Optional extra scripts from pages --}}
    @stack('scripts')
</body>
</html>
