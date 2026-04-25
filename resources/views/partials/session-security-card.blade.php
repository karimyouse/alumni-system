@php
  $supportsDatabaseSessions = $sessionSecuritySupportsDatabaseSessions ?? false;
  $allowMultipleSessions = $sessionSecurityAllowMultipleSessions ?? false;
  $activeSessions = $sessionSecurityActiveSessions ?? collect();
@endphp

<div class="rounded-xl border border-border bg-card p-4 space-y-5 sm:p-6">
  <div>
    <div class="flex items-center gap-2 text-lg font-semibold">
      <i data-lucide="smartphone" class="h-5 w-5 text-primary"></i>
      {{ __('session.title') }}
    </div>
    <p class="mt-1 text-sm text-muted-foreground">
      {{ __('session.subtitle') }}
    </p>
  </div>

  @if(!$supportsDatabaseSessions)
    <div class="rounded-md border border-amber-500/30 bg-amber-500/10 px-3 py-3 text-sm text-amber-700 dark:text-amber-300">
      {{ __('session.not_ready') }}
    </div>
  @else
    <form method="POST" action="{{ route('account.sessions.preference.update') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="allow_multiple_sessions" value="0">

      <label class="flex flex-col gap-4 rounded-lg border border-border bg-background/40 px-4 py-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-1">
          <div class="font-medium">{{ __('session.allow_multiple') }}</div>
          <p class="text-sm text-muted-foreground">
            {{ __('session.allow_multiple_help') }}
          </p>
        </div>

        <input type="checkbox"
               name="allow_multiple_sessions"
               value="1"
               {{ $allowMultipleSessions ? 'checked' : '' }}
               class="mt-1 h-5 w-5 rounded border-input text-primary focus:ring-ring">
      </label>

      <div class="rounded-md border border-border bg-muted/30 px-3 py-3 text-sm text-muted-foreground">
        @if($allowMultipleSessions)
          {{ __('session.multiple_enabled_hint') }}
        @else
          {{ __('session.single_enabled_hint') }}
        @endif
      </div>

      <button type="submit"
              class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 sm:w-auto">
        {{ __('session.save_preference') }}
      </button>
    </form>

    <div class="space-y-4 rounded-lg border border-border bg-background/20 p-4">
      <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="font-medium">{{ __('session.active_devices') }}</div>
          <p class="text-sm text-muted-foreground">
            {{ __('session.active_devices_help') }}
          </p>
        </div>
        <div class="text-sm text-muted-foreground">
          {{ __('session.active_count', ['count' => $activeSessions->count()]) }}
        </div>
      </div>

      <div class="space-y-3">
        @forelse($activeSessions as $session)
          <div class="rounded-lg border border-border bg-card px-4 py-3">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
              <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <div class="font-medium">{{ $session['label'] }}</div>
                  @if($session['is_current'])
                    <span class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                      {{ __('session.current') }}
                    </span>
                  @endif
                </div>

                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                  <span>{{ __('session.last_activity') }}: {{ $session['last_activity_human'] }}</span>
                  <span>{{ __('session.ip_address') }}: {{ $session['ip_address'] }}</span>
                </div>
              </div>
            </div>

            <div class="mt-2 break-all text-xs text-muted-foreground">
              {{ $session['user_agent'] }}
            </div>
          </div>
        @empty
          <div class="rounded-md border border-dashed border-border px-3 py-4 text-sm text-muted-foreground">
            {{ __('session.empty') }}
          </div>
        @endforelse
      </div>

      <div class="flex flex-col gap-3 sm:flex-row">
        <form method="POST" action="{{ route('account.sessions.logoutOthers') }}" class="w-full sm:w-auto">
          @csrf
          <button type="submit"
                  class="w-full rounded-md border border-border px-4 py-2 text-sm font-medium hover:bg-accent/50">
            {{ __('session.logout_others') }}
          </button>
        </form>

        <form method="POST" action="{{ route('account.sessions.logoutAll') }}" class="w-full sm:w-auto"
              onsubmit="return confirm(@json(__('session.logout_all_confirm')));">
          @csrf
          <button type="submit"
                  class="w-full rounded-md border border-destructive/30 bg-destructive/10 px-4 py-2 text-sm font-medium text-destructive hover:bg-destructive/15">
            {{ __('session.logout_all') }}
          </button>
        </form>
      </div>

      <p class="text-xs text-muted-foreground">
        {{ __('session.logout_all_note') }}
      </p>
    </div>
  @endif
</div>
