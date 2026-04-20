@php
  $navUnreadCount = $navUnreadCount ?? 0;
  $navNotifications = $navNotifications ?? collect([]);
  $isRtl = app()->getLocale() === 'ar';
  $menuAlignClass = $isRtl ? 'sm:left-0 sm:right-auto sm:origin-top-left' : 'sm:right-0 sm:left-auto sm:origin-top-right';
  $badgePositionClass = $isRtl ? '-top-1 -left-1' : '-top-1 -right-1';
  $textAlignClass = $isRtl ? 'text-right' : 'text-left';

  $normalizeInternal = function ($u) {
    if (!is_string($u) || trim($u) === '') return null;
    $u = trim($u);

    if (str_starts_with($u, 'http://') || str_starts_with($u, 'https://')) {
      $p = parse_url($u);
      $u = ($p['path'] ?? '/')
         . (isset($p['query']) ? '?'.$p['query'] : '')
         . (isset($p['fragment']) ? '#'.$p['fragment'] : '');
    }
    return $u;
  };
@endphp

@if(auth()->check())
  <div id="notifWrap" class="relative">
    <button id="notifBtn"
            class="relative h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
            aria-label="{{ __('Notifications') }}" aria-expanded="false" type="button">
      <i data-lucide="bell" class="h-4 w-4"></i>

      @if($navUnreadCount > 0)
        <span class="absolute {{ $badgePositionClass }} min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[11px]
                     inline-flex items-center justify-center">
          {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
        </span>
      @endif
    </button>

    <div id="notifMenu"
         class="hidden fixed left-3 right-3 top-16 z-[99999] max-h-[calc(100vh-5rem)] w-auto overflow-hidden rounded-xl border border-border bg-card shadow-2xl sm:absolute sm:top-auto sm:mt-2 sm:w-[380px] sm:max-w-[calc(100vw-2rem)] {{ $menuAlignClass }}">
      <div class="p-4 border-b border-border flex items-center justify-between">
        <div class="font-semibold text-sm">{{ __('Notifications') }}</div>

        @if($navUnreadCount > 0)
          @php $backTo = $normalizeInternal(url()->current()); @endphp
          <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $backTo }}">
            <button type="submit" class="text-xs text-primary hover:underline">
              {{ __('Mark all read') }}
            </button>
          </form>
        @endif
      </div>

      <div class="max-h-[calc(100vh-10rem)] overflow-y-auto divide-y divide-border sm:max-h-[360px]">
        @forelse($navNotifications as $n)
          @php
            $data = is_array($n->data) ? $n->data : [];

            $title = $data['title'] ?? __('Notification');
            $body  = $data['message'] ?? ($data['body'] ?? '');

            $url = $data['action_url']
                ?? ($data['actionUrl'] ?? null)
                ?? ($data['url'] ?? null)
                ?? ($data['link'] ?? null)
                ?? ($data['href'] ?? null);

            if (!$url && !empty($data['ticket_id'])) {
              $tid = (int)$data['ticket_id'];
              $role = auth()->user()->role ?? '';
              if (in_array($role, ['admin','super_admin'], true)) {
                $url = "/admin/support?status=all#ticket-{$tid}";
              } elseif (\Illuminate\Support\Facades\Route::has('support.tickets.show')) {
                $url = route('support.tickets.show', $tid);
              }
            }

            $redirectTo = $normalizeInternal($url ?: url()->current());

            $icon  = $data['icon'] ?? 'bell';
            $isUnread = is_null($n->read_at);
            $time = $n->created_at ? $n->created_at->diffForHumans() : '';
          @endphp

          <form method="POST" action="{{ route('notifications.read', $n->id) }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

            <button type="submit"
                    class="w-full {{ $textAlignClass }} p-4 hover:bg-accent/40 transition flex gap-3 {{ $isRtl ? 'flex-row-reverse' : '' }}">
              <span class="mt-1 w-2 h-2 rounded-full {{ $isUnread ? 'bg-primary' : 'bg-muted' }}"></span>

              <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
              </div>

              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium leading-snug break-words sm:truncate">{{ $title }}</div>
                @if($body)
                  <div class="text-xs text-muted-foreground mt-1 line-clamp-2">{{ $body }}</div>
                @endif
                <div class="text-[11px] text-muted-foreground mt-2">{{ $time }}</div>
              </div>
            </button>
          </form>
        @empty
          <div class="p-6 text-sm text-muted-foreground {{ $textAlignClass }}">{{ __('No notifications.') }}</div>
        @endforelse
      </div>
    </div>
  </div>

  <script>
    (function () {
      const wrap = document.getElementById('notifWrap');
      if (!wrap) return;

      const btn = document.getElementById('notifBtn');
      const menu = document.getElementById('notifMenu');

      menu.addEventListener('click', (e) => e.stopPropagation());

      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        document.querySelectorAll('[data-lang-menu]').forEach(function (langMenu) {
          langMenu.classList.add('hidden');
        });
        const isOpening = menu.classList.contains('hidden');
        menu.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
      });

      document.addEventListener('click', function (e) {
        if (!wrap.contains(e.target)) {
          menu.classList.add('hidden');
          btn.setAttribute('aria-expanded', 'false');
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          menu.classList.add('hidden');
          btn.setAttribute('aria-expanded', 'false');
        }
      });
    })();
  </script>
@endif
