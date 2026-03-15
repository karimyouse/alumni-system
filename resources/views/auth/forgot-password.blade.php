@extends('layouts.app')

@php
  $title = __('Forgot Password');
@endphp

@section('content')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center">

  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>

  <div class="absolute top-4 left-4 flex items-center gap-2 text-sm text-muted-foreground">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 hover:text-foreground transition">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      <span>{{ __("Back") }}</span>
    </a>
  </div>

  <div class="relative z-10 w-full max-w-md rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-primary text-primary-foreground flex items-center justify-center mb-3">
        <i data-lucide="key-round" class="w-6 h-6"></i>
      </div>

      <h1 class="text-2xl font-bold">{{ __("Forgot Password") }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ __("We will send a password reset link to your registered email.") }}
      </p>
    </div>


    @if ($errors->any())
      <div class="mb-5 rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
        <div class="font-semibold text-destructive mb-1 inline-flex items-center gap-2">
          <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          {{ __("Cannot continue") }}
        </div>
        <div class="text-destructive/90">{{ $errors->first() }}</div>
      </div>
    @endif


    @if (session('toast_success'))
      <div class="mb-5 rounded-xl border border-green-500/25 bg-green-500/10 p-4 text-sm">
        <div class="font-semibold text-green-400 mb-1 inline-flex items-center gap-2">
          <i data-lucide="check-circle-2" class="h-4 w-4"></i>
          {{ __("Success") }}
        </div>
        <div class="text-green-300/90">{{ session('toast_success') }}</div>
      </div>
    @endif


    @if (app()->environment('local') && session('reset_link'))
      <div class="mb-5 rounded-xl border border-border bg-accent/10 p-4 text-sm">
        <div class="font-semibold mb-2 inline-flex items-center gap-2">
          <i data-lucide="link" class="h-4 w-4"></i>
          {{ __("Reset Link (Local Only)") }}
        </div>

        <div class="text-xs text-muted-foreground break-all mb-3">
          {{ session('reset_link') }}
        </div>

        <button type="button"
                class="rounded-md border border-border px-3 py-2 text-xs hover:bg-accent/40"
                onclick="copyResetLink('{{ addslashes(session('reset_link')) }}')">
          {{ __("Copy link") }}
        </button>
      </div>
    @endif


    <div class="grid grid-cols-4 gap-1 rounded-lg bg-muted p-1 mb-6">
      <button type="button" data-role="alumni" class="role-tab active">
        <i data-lucide="graduation-cap"></i><span>{{ __("Alumni") }}</span>
      </button>
      <button type="button" data-role="college" class="role-tab">
        <i data-lucide="building-2"></i><span>{{ __("College") }}</span>
      </button>
      <button type="button" data-role="company" class="role-tab">
        <i data-lucide="briefcase"></i><span>{{ __("Company") }}</span>
      </button>
      <button type="button" data-role="admin" class="role-tab">
        <i data-lucide="shield-check"></i><span>{{ __("Admin") }}</span>
      </button>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4" id="forgot-form">
      @csrf
      <input type="hidden" name="role" id="role-input" value="{{ old('role', $role ?? 'alumni') }}">

      <div>
        <label class="text-sm font-medium" id="identifier-label">{{ __("Academic ID") }}</label>
        <input
          type="text"
          name="identifier"
          id="identifier-input"
          value="{{ old('identifier', $identifier ?? '') }}"
          placeholder="{{ __('Enter your academic ID') }}"
          class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
          required
        />
        @error('identifier')
          <div class="text-xs text-destructive mt-1">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit"
        class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        {{ __("Send Reset Link") }}
      </button>

      <div class="text-center text-xs text-muted-foreground">
        {{ __("If the account exists, the link will be sent to the registered email.") }}
      </div>


      <div class="rounded-xl border border-border bg-background/40 p-4 text-sm">
        <div class="font-semibold mb-1">{{ __("Need help?") }}</div>
        <div class="text-xs text-muted-foreground mb-3">
          {{ __("If you didn’t receive the email, you can contact support or track your request.") }}
        </div>

        <div class="flex items-center justify-between gap-3">
          @if(\Illuminate\Support\Facades\Route::has('support.request.show'))
            <a class="text-sm text-primary hover:underline"
               href="{{ route('support.request.show', ['role'=>old('role', $role ?? 'alumni'), 'identifier'=>old('identifier', $identifier ?? '')]) }}">
              {{ __("Contact Support?") }}
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('support.track.show'))
            <a class="text-sm text-muted-foreground hover:text-foreground hover:underline"
               href="{{ route('support.track.show') }}">
              {{ __("Track request") }}
            </a>
          @endif
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  const tabs = document.querySelectorAll('.role-tab');
  const label = document.getElementById('identifier-label');
  const input = document.getElementById('identifier-input');
  const roleInput = document.getElementById('role-input');

  const config = {
    alumni:  { label: @json(__('Academic ID')),   placeholder: @json(__('Enter your academic ID')), type: 'text'  },
    college: { label: @json(__('Email Address')), placeholder: @json(__('Enter college email')),    type: 'email' },
    company: { label: @json(__('Email Address')), placeholder: @json(__('Enter company email')),    type: 'email' },
    admin:   { label: @json(__('Email Address')), placeholder: @json(__('Enter admin email')),      type: 'email' },
  };

  function setRole(role) {
    roleInput.value = role;
    const c = config[role] || config.alumni;
    label.innerText = c.label;
    input.placeholder = c.placeholder;
    input.type = c.type;
  }

  function setActiveTab(role) {
    tabs.forEach(t => t.classList.remove('active'));
    const btn = document.querySelector(`.role-tab[data-role="${role}"]`);
    if (btn) btn.classList.add('active');
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      setActiveTab(tab.dataset.role);
      setRole(tab.dataset.role);
    });
  });

  const initialRole = (roleInput.value || 'alumni').toLowerCase();
  setActiveTab(initialRole);
  setRole(initialRole);

  function copyResetLink(text) {
    if (!text) return;
    const showMiniToast = (msg) => {
      const el = document.createElement('div');
      el.className = 'fixed bottom-6 right-6 z-[99999] rounded-xl border border-border bg-card shadow-xl px-4 py-3 text-sm';
      el.innerText = msg;
      document.body.appendChild(el);
      setTimeout(() => el.remove(), 1500);
    };

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(() => showMiniToast(@json(__('Copied')))).catch(() => {});
      return;
    }

    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); showMiniToast(@json(__('Copied'))); } catch (e) {}
    ta.remove();
  }
</script>
@endsection
