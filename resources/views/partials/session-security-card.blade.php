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

    <div class="space-y-4 rounded-lg border border-border bg-background/20 p-4 sm:p-5">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
          <div class="font-medium">{{ __('session.active_devices') }}</div>
          <p class="text-sm text-muted-foreground">
            {{ __('session.active_devices_help') }}
          </p>
        </div>
        <div class="inline-flex w-fit items-center rounded-full border border-border bg-card px-3 py-1 text-sm text-muted-foreground">
          {{ __('session.active_count', ['count' => $activeSessions->count()]) }}
        </div>
      </div>

      @if($activeSessions->isEmpty())
        <div class="rounded-md border border-dashed border-border px-3 py-4 text-sm text-muted-foreground">
          {{ __('session.empty') }}
        </div>
      @else
        <div class="hidden overflow-x-auto rounded-lg border border-border xl:block">
          <table class="min-w-[52rem] table-fixed divide-y divide-border">
            <thead class="bg-background/60">
              <tr class="text-left text-xs uppercase tracking-wide text-muted-foreground">
                <th class="w-[16%] px-4 py-3 font-medium">{{ __('session.os') }}</th>
                <th class="w-[34%] px-4 py-3 font-medium">{{ __('session.browser') }}</th>
                <th class="w-[18%] px-4 py-3 font-medium">{{ __('session.ip_address') }}</th>
                <th class="w-[16%] px-4 py-3 font-medium">{{ __('session.last_session') }}</th>
                <th class="w-[16%] px-4 py-3 text-right font-medium">{{ __('session.action') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border bg-card">
              @foreach($activeSessions as $session)
                <tr class="align-top">
                  <td class="px-4 py-4 text-sm">
                    <div class="font-medium">{{ $session['platform'] }}</div>
                    <div class="mt-1 text-xs text-muted-foreground">{{ $session['device_type'] }}</div>
                  </td>
                  <td class="px-4 py-4 text-sm">
                    <div class="font-medium">{{ $session['browser'] }}</div>
                    <div class="mt-1 break-words text-xs leading-5 text-muted-foreground" title="{{ $session['user_agent'] }}">
                      {{ $session['user_agent'] }}
                    </div>
                  </td>
                  <td class="px-4 py-4 text-sm text-muted-foreground break-words">{{ $session['ip_address'] }}</td>
                  <td class="px-4 py-4 text-sm">
                    @if($session['is_current'])
                      <span class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ __('session.this_device') }}
                      </span>
                    @else
                      <span class="text-muted-foreground">{{ $session['last_activity_human'] }}</span>
                    @endif
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex justify-end">
                      @if($session['can_delete'])
                        <form method="POST"
                              action="{{ route('account.sessions.remove', ['sessionId' => $session['id']]) }}"
                              onsubmit="return confirm(@json(__('session.remove_device_confirm')));">
                          @csrf
                          <button type="submit"
                                  class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-border text-muted-foreground transition hover:border-destructive/40 hover:bg-destructive/10 hover:text-destructive"
                                  title="{{ __('session.remove_device') }}"
                                  aria-label="{{ __('session.remove_device') }}">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                          </button>
                        </form>
                      @else
                        <span class="inline-flex min-w-[8.5rem] items-center justify-center whitespace-nowrap rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                          {{ __('session.current') }}
                        </span>
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="space-y-3 xl:hidden">
          @foreach($activeSessions as $session)
            <div class="rounded-lg border border-border bg-card px-4 py-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <div class="font-medium">{{ $session['platform'] }}</div>
                    <span class="rounded-full bg-muted px-2 py-0.5 text-[11px] text-muted-foreground">
                      {{ $session['device_type'] }}
                    </span>
                  </div>
                  <div class="mt-1 text-sm text-muted-foreground">{{ $session['browser'] }}</div>
                </div>

                @if($session['can_delete'])
                  <form method="POST"
                        action="{{ route('account.sessions.remove', ['sessionId' => $session['id']]) }}"
                        onsubmit="return confirm(@json(__('session.remove_device_confirm')));">
                    @csrf
                    <button type="submit"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-border text-muted-foreground transition hover:border-destructive/40 hover:bg-destructive/10 hover:text-destructive"
                            title="{{ __('session.remove_device') }}"
                            aria-label="{{ __('session.remove_device') }}">
                      <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                  </form>
                @else
                  <span class="inline-flex min-w-[7rem] items-center justify-center whitespace-nowrap rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                    {{ __('session.current') }}
                  </span>
                @endif
              </div>

              <div class="mt-3 grid gap-2 text-xs text-muted-foreground sm:grid-cols-2">
                <div class="break-words">{{ __('session.ip_address') }}: {{ $session['ip_address'] }}</div>
                <div>
                  {{ __('session.last_session') }}:
                  @if($session['is_current'])
                    {{ __('session.this_device') }}
                  @else
                    {{ $session['last_activity_human'] }}
                  @endif
                </div>
                <div class="break-words sm:col-span-2">{{ $session['user_agent'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @endif

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
