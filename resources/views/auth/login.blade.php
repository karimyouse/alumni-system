@extends('layouts.app')

@section('content')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center">


  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>


  <div class="absolute top-4 left-4 flex items-center gap-2 text-sm text-muted-foreground">
    <a href="/" class="inline-flex items-center gap-2 hover:text-foreground transition">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      <span>Home</span>
    </a>
  </div>

  <div class="absolute top-4 right-4 flex items-center gap-2">
    <button type="button"
            class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
            aria-label="Language">
      <i data-lucide="globe" class="h-4 w-4"></i>
    </button>

    <button type="button"
            class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
            data-theme-toggle aria-label="Theme">
      <i data-lucide="sun" class="h-4 w-4"></i>
    </button>
  </div>


  <div class="relative z-10 w-full max-w-md rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-primary text-primary-foreground flex items-center justify-center mb-3">
        <i data-lucide="graduation-cap" class="w-6 h-6"></i>
      </div>
      <h1 class="text-2xl font-bold">Welcome Back</h1>
      <p class="text-sm text-muted-foreground">Sign in to your account</p>
    </div>


    <div class="grid grid-cols-4 gap-1 rounded-lg bg-muted p-1 mb-6">
      <button type="button" data-role="alumni" class="role-tab active">
        <i data-lucide="graduation-cap"></i>
        <span>Alumni</span>
      </button>

      <button type="button" data-role="college" class="role-tab">
        <i data-lucide="building-2"></i>
        <span>College</span>
      </button>

      <button type="button" data-role="company" class="role-tab">
        <i data-lucide="briefcase"></i>
        <span>Company</span>
      </button>

      <button type="button" data-role="admin" class="role-tab">
        <i data-lucide="shield-check"></i>
        <span>Super Admin</span>
      </button>
    </div>


    <form method="POST" action="/login" class="space-y-4" id="login-form">
  @csrf

      <input type="hidden" name="role" id="role-input" value="alumni">


      <div>
        <label class="text-sm font-medium" id="identifier-label">Academic ID</label>
        <input
          type="text"
          name="identifier"
          id="identifier-input"
          placeholder="Enter your academic ID"
          class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
        />
      </div>


      <div>
        <div class="flex items-center justify-between">
          <label class="text-sm font-medium">Password</label>
          <a href="#" class="text-sm text-primary hover:underline">Forgot Password?</a>
        </div>
        <input
          type="password"
          name="password"
          placeholder="••••••••"
          class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
        />
      </div>


      <button
        type="submit"
        class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        Sign In
      </button>

      
      <div id="company-extra" class="hidden">
        <div class="my-6 border-t border-border"></div>

        <div class="text-center text-sm text-muted-foreground mb-3">
          Don't have an account?
        </div>

        <a href="/register" class="block">
          <button type="button"
                  class="w-full rounded-md border border-border bg-background/20 px-4 py-2 font-medium hover:bg-accent/40 transition">
            Create Account
          </button>
        </a>

        <div class="mt-3 text-center text-xs text-muted-foreground">
          Company registration requires admin approval
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

  const config = {
    alumni: { label: 'Academic ID', placeholder: 'Enter your academic ID', type: 'text', showCompany: false },
    college: { label: 'Email Address', placeholder: 'Enter college email', type: 'email', showCompany: false },
    company: { label: 'Email Address', placeholder: 'Enter company email', type: 'email', showCompany: true },
    admin: { label: 'Email Address', placeholder: 'Enter admin ID or email', type: 'text', showCompany: false },
  };

  function setRole(role) {
    roleInput.value = role;
    const c = config[role];

    label.innerText = c.label;
    input.placeholder = c.placeholder;
    input.type = c.type;

    if (c.showCompany) companyExtra.classList.remove('hidden');
    else companyExtra.classList.add('hidden');
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      setRole(tab.dataset.role);
    });
  });

  setRole('alumni');
</script>
@endsection
