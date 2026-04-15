@php
  $passwordChangedAt = auth()->user()?->password_changed_at;
@endphp

<form method="POST" action="{{ route('account.password.update') }}"
      class="rounded-xl border border-border bg-card p-4 space-y-4 sm:p-6">
  @csrf

  <div>
    <div class="flex items-center gap-2 text-lg font-semibold">
      <i data-lucide="lock-keyhole" class="h-5 w-5 text-primary"></i>
      Change Password
    </div>
    <p class="mt-1 text-sm text-muted-foreground">
      Enter your current password before setting a new one.
    </p>
    @if($passwordChangedAt)
      <p class="mt-1 text-xs text-muted-foreground">
        Last changed {{ $passwordChangedAt->format('M d, Y') }}.
      </p>
    @endif
  </div>

  <div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
      <label class="text-sm font-medium">Current Password</label>
      <input type="password"
             name="current_password"
             autocomplete="current-password"
             class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
             required>
      @error('current_password')
        <div class="mt-1 text-xs text-destructive">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label class="text-sm font-medium">New Password</label>
      <input type="password"
             name="password"
             autocomplete="new-password"
             class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
             required>
      @error('password')
        <div class="mt-1 text-xs text-destructive">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label class="text-sm font-medium">Confirm New Password</label>
      <input type="password"
             name="password_confirmation"
             autocomplete="new-password"
             class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
             required>
    </div>
  </div>

  <div class="rounded-md border border-border bg-muted/30 px-3 py-2 text-xs text-muted-foreground">
    Use at least 8 characters with letters and numbers.
  </div>

  <button type="submit"
          class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 sm:w-auto">
    Update Password
  </button>
</form>
