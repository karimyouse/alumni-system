<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Dashboard' }}</title>


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

<body class="h-screen overflow-hidden bg-background text-foreground">
@php
  $user = auth()->user();
  $userRole = $user?->role ? str_replace('_', ' ', $user->role) : '';
  $subLine = ($user?->role === 'alumni') ? ($user?->academic_id ?? '') : ($user?->email ?? '');
  $initials = $user?->name ? collect(explode(' ', $user->name))->map(fn($n)=>mb_substr($n,0,1))->join('') : 'U';
@endphp

<div class="h-screen w-full flex overflow-hidden">


  <aside id="sidebar"
         class="hidden md:flex w-64 flex-col border-r border-border bg-card
                h-screen flex-shrink-0 overflow-hidden">

    <div class="border-b p-4 flex-shrink-0">
      <a href="/" class="flex items-center gap-3">
        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-primary text-primary-foreground">
          <i data-lucide="graduation-cap" class="h-5 w-5"></i>
        </div>
        <div class="flex flex-col">
          <span class="font-semibold text-sm">Alumni System</span>
          <span class="text-xs text-muted-foreground capitalize">{{ $role ?? $userRole }}</span>
        </div>
      </a>
    </div>


    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
      @foreach(($nav ?? []) as $item)
        @php
          $hrefPath = ltrim($item['href'], '/');
          if (!str_contains($hrefPath, '/')) $active = request()->is($hrefPath);
          else $active = request()->is($hrefPath) || request()->is($hrefPath.'/*');
        @endphp

        <a href="{{ $item['href'] }}"
           class="flex items-center justify-between rounded-md px-3 py-2 text-sm transition
           {{ $active ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
          <span class="flex items-center gap-3">
            <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="h-4 w-4"></i>
            <span>{{ $item['label'] }}</span>
          </span>

          @if(isset($item['badge']) && $item['badge'] > 0)
            <span class="inline-flex items-center justify-center rounded-full bg-secondary px-2 py-0.5 text-[11px] text-secondary-foreground">
              {{ $item['badge'] }}
            </span>
          @endif
        </a>
      @endforeach
    </nav>


    <div class="border-t p-4 flex-shrink-0">
      <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-semibold">
          {{ $initials }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium truncate">{{ $user?->name }}</p>
          <p class="text-xs text-muted-foreground truncate">{{ $subLine }}</p>
        </div>
      </div>

      <form method="POST" action="/logout">
        @csrf
        <button type="submit"
                class="w-full justify-start gap-2 inline-flex items-center rounded-md px-3 py-2 text-sm hover:bg-accent/50 transition">
          <i data-lucide="log-out" class="h-4 w-4"></i>
          Logout
        </button>
      </form>
    </div>
  </aside>


  <div class="flex-1 flex flex-col h-screen overflow-hidden">

    <header class="flex-shrink-0 z-40 flex items-center justify-between gap-4 border-b bg-background px-4 h-14">
      <div class="flex items-center gap-4">
        <button type="button"
                class="md:hidden h-9 w-9 inline-flex items-center justify-center rounded-md border border-border"
                onclick="document.getElementById('sidebar').classList.toggle('hidden')">
          <i data-lucide="panel-left" class="h-4 w-4"></i>
        </button>
        <h1 class="text-lg font-semibold">{{ $title ?? '' }}</h1>
      </div>

      <div class="flex items-center gap-2">
        <button class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50" aria-label="Notifications">
          <i data-lucide="bell" class="h-4 w-4"></i>
        </button>
        <button class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50" aria-label="Language">
          <i data-lucide="globe" class="h-4 w-4"></i>
        </button>
        <button class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
        data-theme-toggle aria-label="Theme">
        <i data-lucide="moon" class="h-4 w-4"></i>
</button>

      </div>
    </header>


    <main class="flex-1 overflow-y-auto p-4 md:p-6">
      @yield('content')
    </main>
  </div>

</div>


@if(session('toast_success'))
  <div id="toast" class="fixed bottom-6 right-6 z-50 w-[320px] rounded-xl border border-border bg-card shadow-xl">
    <div class="p-4">
      <div class="font-semibold">Success</div>
      <div class="text-sm text-muted-foreground">{{ session('toast_success') }}</div>
    </div>
  </div>
  <script>
    setTimeout(()=>{ const t=document.getElementById('toast'); if(t) t.remove(); }, 3500);
  </script>
@endif

</body>
</html>
