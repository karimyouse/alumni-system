@extends('layouts.app')

@php
  $adminPrimaryHsl = $appTheme['primary_hsl'] ?? '217 91% 60%';
  $adminPrimaryHex = $appSettings->primary_color ?? '#2563eb';
@endphp

@push('head')
<style>
  .role-tab{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:.35rem;
    min-height:3.5rem;
    border-radius:.5rem;
    font-size:.875rem;
    color:hsl(var(--muted-foreground));
    transition:all .2s ease;
    border:1px solid transparent;
  }

  .role-tab i{
    width:1rem;
    height:1rem;
  }

  .role-tab.active{
    background: hsl(var(--primary) / 0.14);
    color: hsl(var(--primary));
    border-color: hsl(var(--primary) / 0.28);
    box-shadow: inset 0 0 0 1px hsl(var(--primary) / 0.10);
  }

  .role-tab:hover{
    color:hsl(var(--foreground));
    background:hsl(var(--accent));
  }

  .login-shell{
    transition: all .25s ease;
  }

  .login-role-glow{
    position:absolute;
    inset:0;
    pointer-events:none;
    background:
      radial-gradient(circle at center, hsl(var(--primary) / 0.14) 0%, transparent 58%);
    transition: all .25s ease;
  }

  .login-role-icon{
    transition: all .25s ease;
    box-shadow: 0 0 0 1px hsl(var(--primary) / 0.10), 0 10px 30px hsl(var(--primary) / 0.18);
  }

  .login-company-extra{
    border-top:1px solid hsl(var(--border));
    padding-top:1.25rem;
  }
</style>
@endpush

@section('content')
<div class="min-h-screen relative flex items-center justify-center">

  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>

  @php($isRtl = app()->getLocale() === 'ar')
  <div class="absolute top-4 {{ $isRtl ? 'right-4' : 'left-4' }} flex items-center gap-2 text-sm text-muted-foreground">
    <a href="/" class="inline-flex items-center gap-2 hover:text-foreground transition {{ $isRtl ? 'flex-row-reverse' : '' }}">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      <span>{{ __("Home") }}</span>
    </a>
  </div>

  <div class="absolute top-4 {{ $isRtl ? 'left-4' : 'right-4' }} flex items-center gap-2">
    @include('partials.language-dropdown', [
      'buttonClass' => 'h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50',
      'buttonLabel' => __('Language'),
      'menuWidth' => 'w-36',
      'menuAlignClass' => $isRtl ? 'left-0 origin-top-left' : 'right-0 origin-top-right',
      'menuTextAlignClass' => app()->getLocale() === 'ar' ? 'text-right' : 'text-left',
    ])

    <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50" data-theme-toggle aria-label="{{ __('Theme') }}">
      <i data-lucide="sun" class="h-4 w-4"></i>
    </button>
  </div>

  <div class="relative z-10 w-full max-w-md rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl login-shell" id="login-shell">
    <div class="login-role-glow rounded-xl"></div>

    <div class="relative flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-primary text-primary-foreground flex items-center justify-center mb-3 login-role-icon">
        <i data-lucide="graduation-cap" class="w-6 h-6"></i>
      </div>
      <h1 class="text-2xl font-bold">{{ __("Welcome Back") }}</h1>
      <p class="text-sm text-muted-foreground">{{ __("Sign in to your account") }}</p>
    </div>

    @if ($errors->any())
      <div class="mb-5 rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
        <div class="font-semibold text-destructive mb-1 inline-flex items-center gap-2">
          <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          {{ __("Cannot sign in") }}
        </div>

        <div class="text-destructive/90">
          {{ $errors->first() }}
        </div>

        @if(\Illuminate\Support\Facades\Route::has('support.request.show'))
          <div class="mt-3">
            <a href="{{ route('support.request.show', ['role'=>old('role','alumni'), 'identifier'=>old('identifier','')]) }}"
               class="inline-flex items-center gap-2 text-sm text-primary hover:underline">
              <i data-lucide="help-circle" class="h-4 w-4"></i>
              {{ __("Contact Support") }}
            </a>
          </div>
        @endif
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

    <form method="POST" action="{{ route('login') }}" class="space-y-4 relative z-10" id="login-form">
      @csrf
      <input type="hidden" name="role" id="role-input" value="{{ old('role', 'alumni') }}">

      <div>
        <label class="text-sm font-medium" id="identifier-label">{{ __("Academic ID") }}</label>
        <input
          type="text"
          name="identifier"
          id="identifier-input"
          value="{{ old('identifier') }}"
          placeholder="{{ __('Enter your academic ID') }}"
          class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
        />
        @error('identifier')
          <div class="text-xs text-destructive mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div>
        <div class="flex items-center justify-between gap-3">
          <label class="text-sm font-medium">{{ __("Password") }}</label>

          <a href="{{ route('password.request') }}" id="forgot-link" class="text-sm text-primary hover:underline">
            {{ __("Forgot Password?") }}
          </a>
        </div>

        <input
          type="password"
          name="password"
          placeholder="••••••••"
          class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
        />
        @error('password')
          <div class="text-xs text-destructive mt-1">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        {{ __("Sign In") }}
      </button>

      @if(\Illuminate\Support\Facades\Route::has('support.request.show'))
        <div class="text-center pt-1">
          <a href="{{ route('support.request.show', ['role'=>old('role','alumni'), 'identifier'=>old('identifier','')]) }}"
             class="inline-flex items-center gap-2 text-sm text-primary hover:underline">
            <i data-lucide="life-buoy" class="h-4 w-4"></i>
            {{ __("Need help? Contact Support") }}
          </a>
        </div>
      @endif

      <div id="company-extra" class="hidden login-company-extra">
        <div class="text-center text-sm text-muted-foreground mb-3">
          {{ __("Don't have an account?") }}
        </div>

        <a href="/register" class="block">
          <button type="button" class="w-full rounded-md border border-border bg-background/20 px-4 py-2 font-medium hover:bg-accent/40 transition">
            {{ __("Create Account") }}
          </button>
        </a>

        <div class="mt-3 text-center text-xs text-muted-foreground">
          {{ __("Company registration requires admin approval") }}
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
  const companyExtra = document.getElementById('company-extra');
  const forgotLink = document.getElementById('forgot-link');

  const config = {
    alumni: {
      label: @json(__('Academic ID')),
      placeholder: @json(__('Enter your academic ID')),
      type: 'text',
      showCompany: false,
      primary: '217 91% 60%',
      ring: '217 91% 60%',
      foreground: '0 0% 100%',
      hex: '#2563eb'
    },
    college: {
      label: @json(__('Email Address')),
      placeholder: @json(__('Enter college email')),
      type: 'email',
      showCompany: false,
      primary: '142 71% 45%',
      ring: '142 71% 45%',
      foreground: '0 0% 100%',
      hex: '#22c55e'
    },
    company: {
      label: @json(__('Email Address')),
      placeholder: @json(__('Enter company email')),
      type: 'email',
      showCompany: true,
      primary: '262 83% 58%',
      ring: '262 83% 58%',
      foreground: '0 0% 100%',
      hex: '#7c3aed'
    },
    admin: {
      label: @json(__('Email Address')),
      placeholder: @json(__('Enter admin email')),
      type: 'email',
      showCompany: false,
      primary: @json($adminPrimaryHsl),
      ring: @json($adminPrimaryHsl),
      foreground: '0 0% 100%',
      hex: @json($adminPrimaryHex)
    },
  };

  function applyRoleTheme(role) {
    const c = config[role] || config.alumni;

    document.documentElement.style.setProperty('--primary', c.primary, 'important');
    document.documentElement.style.setProperty('--ring', c.ring, 'important');
    document.documentElement.style.setProperty('--primary-foreground', c.foreground, 'important');

    const metaTheme = document.querySelector('meta[name="theme-color"]');
    if (metaTheme) {
      metaTheme.setAttribute('content', c.hex || '#2563eb');
    }
  }

  function updateForgotHref() {
    if (!forgotLink) return;
    const r = (roleInput.value || 'alumni');
    const id = (input.value || '');
    forgotLink.href = `{{ route('password.request') }}?role=${encodeURIComponent(r)}&identifier=${encodeURIComponent(id)}`;
  }

  function setRole(role) {
    roleInput.value = role;
    const c = config[role] || config.alumni;

    label.innerText = c.label;
    input.placeholder = c.placeholder;
    input.type = c.type;

    if (c.showCompany) companyExtra.classList.remove('hidden');
    else companyExtra.classList.add('hidden');

    applyRoleTheme(role);
    updateForgotHref();
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

  input.addEventListener('input', updateForgotHref);

  const initialRole = (roleInput.value || 'alumni').toLowerCase();
  setActiveTab(initialRole);
  setRole(initialRole);
</script>
@endsection
