@extends('layouts.dashboard')

@php
  $title = 'Users';
  $role  = 'Admin';

  $nav = [
    ['label'=>'Overview', 'href'=>'/admin', 'icon'=>'layout-dashboard'],
    ['label'=>'Users',    'href'=>'/admin/users', 'icon'=>'users'],
    ['label'=>'Content',  'href'=>'/admin/content', 'icon'=>'file-text'],
    ['label'=>'Reports',  'href'=>'/admin/reports', 'icon'=>'bar-chart-3'],
    ['label'=>'Settings', 'href'=>'/admin/settings', 'icon'=>'settings'],
    ['label'=>'Support',  'href'=>'/admin/support', 'icon'=>'help-circle'],
  ];

  $users = [
    'alumni' => [
      ['id'=>'1','name'=>'Ahmed Al-Hassan','email'=>'ahmed@example.com','status'=>'active','lastLogin'=>'Dec 22, 2025'],
      ['id'=>'2','name'=>'Sara Ali','email'=>'sara@example.com','status'=>'active','lastLogin'=>'Dec 21, 2025'],
      ['id'=>'3','name'=>'Omar Khalil','email'=>'omar@example.com','status'=>'suspended','lastLogin'=>'Dec 15, 2025'],
    ],
    'college' => [
      ['id'=>'4','name'=>'Dr. Mohammad Salem','email'=>'college@ptc.edu','status'=>'active','lastLogin'=>'Dec 22, 2025'],
    ],
    'company' => [
      ['id'=>'5','name'=>'TechCorp HR','email'=>'company@techcorp.com','status'=>'active','lastLogin'=>'Dec 20, 2025'],
      ['id'=>'6','name'=>'StartupX Recruiting','email'=>'hr@startupx.com','status'=>'pending','lastLogin'=>'N/A'],
    ],
  ];

  $tabs = [
    ['key'=>'alumni','label'=>'Alumni'],
    ['key'=>'college','label'=>'College'],
    ['key'=>'company','label'=>'Company'],
  ];
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Users</h1>
      <p class="text-muted-foreground">Manage accounts across all roles</p>
    </div>

    <div class="flex items-center gap-2 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-72">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
        <input class="w-full rounded-md border border-input bg-background/60 pl-9 pr-3 py-2 text-sm"
               placeholder="Search users..." />
      </div>
      <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
        Add User
      </button>
    </div>
  </div>

  
  <div class="inline-flex rounded-lg bg-muted p-1 gap-1" id="tabs-users">
    @foreach($tabs as $i => $t)
      <button type="button"
              class="px-3 py-2 text-sm rounded-md {{ $i==0 ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground' }}"
              data-tab="{{ $t['key'] }}">
        {{ $t['label'] }} ({{ count($users[$t['key']]) }})
      </button>
    @endforeach
  </div>

  @foreach($tabs as $i => $t)
    <div class="tab-panel-users {{ $i==0 ? '' : 'hidden' }}" data-panel="{{ $t['key'] }}">
      <div class="rounded-xl border border-border bg-card overflow-hidden">
        <div class="overflow-auto">
          <table class="w-full">
            <thead class="border-b bg-muted/50">
              <tr>
                <th class="text-left p-4 font-medium">Name</th>
                <th class="text-left p-4 font-medium">Email</th>
                <th class="text-left p-4 font-medium">Status</th>
                <th class="text-left p-4 font-medium">Last Login</th>
                <th class="text-left p-4 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users[$t['key']] as $u)
                <tr class="border-b last:border-0">
                  <td class="p-4 font-medium">{{ $u['name'] }}</td>
                  <td class="p-4 text-sm text-muted-foreground">{{ $u['email'] }}</td>
                  <td class="p-4">
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs
                      {{ $u['status']==='active' ? 'bg-green-500/10 text-green-400' : ($u['status']==='pending' ? 'bg-secondary text-secondary-foreground' : 'bg-red-500/15 text-red-400') }}">
                      {{ $u['status'] }}
                    </span>
                  </td>
                  <td class="p-4 text-sm text-muted-foreground">{{ $u['lastLogin'] }}</td>
                  <td class="p-4">
                    <div class="flex gap-2">
                      <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">View</button>
                      <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Edit</button>
                      <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Disable</button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endforeach
</div>

<script>
  const tabs = document.getElementById('tabs-users');
  const btns = tabs.querySelectorAll('[data-tab]');
  const panels = document.querySelectorAll('.tab-panel-users');

  function setActive(tab) {
    btns.forEach(b => {
      const on = b.dataset.tab === tab;
      b.classList.toggle('bg-background', on);
      b.classList.toggle('shadow-sm', on);
      b.classList.toggle('text-foreground', on);
      b.classList.toggle('text-muted-foreground', !on);
    });
    panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== tab));
  }

  btns.forEach(b => b.addEventListener('click', () => setActive(b.dataset.tab)));
</script>
@endsection
