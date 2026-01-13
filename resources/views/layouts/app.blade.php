<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Alumni Tracking System' }}</title>


    <script>
      (function () {
        try {
          const theme = localStorage.getItem('theme');
          if (theme === 'dark') document.documentElement.classList.add('dark');
        } catch (e) {}
      })();
    </script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background text-foreground">
    
    @yield('content')
</body>
</html>
