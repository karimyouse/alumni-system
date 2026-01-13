@extends('layouts.dashboard')

@php
  $title = 'Support';
  $role  = 'Admin';

  $nav = [
    ['label'=>'Overview', 'href'=>'/admin', 'icon'=>'layout-dashboard'],
    ['label'=>'Users',    'href'=>'/admin/users', 'icon'=>'users'],
    ['label'=>'Content',  'href'=>'/admin/content', 'icon'=>'file-text'],
    ['label'=>'Reports',  'href'=>'/admin/reports', 'icon'=>'bar-chart-3'],
    ['label'=>'Settings', 'href'=>'/admin/settings', 'icon'=>'settings'],
    ['label'=>'Support',  'href'=>'/admin/support', 'icon'=>'help-circle'],
  ];

  $tickets = [
    ['id'=>'1','subject'=>'Cannot update profile','user'=>'Ahmed Hassan','email'=>'ahmed@example.com','status'=>'open','priority'=>'medium','date'=>'Dec 22, 2025'],
    ['id'=>'2','subject'=>'Login issues','user'=>'Sara Ali','email'=>'sara@example.com','status'=>'in_progress','priority'=>'high','date'=>'Dec 21, 2025'],
    ['id'=>'3','subject'=>'Job posting not visible','user'=>'TechCorp HR','email'=>'company@techcorp.com','status'=>'open','priority'=>'low','date'=>'Dec 20, 2025'],
    ['id'=>'4','subject'=>'Workshop registration error','user'=>'Omar Khalil','email'=>'omar@example.com','status'=>'resolved','priority'=>'medium','date'=>'Dec 18, 2025'],
  ];

  $badge = fn($status) => match($status){
    'open' => ['Open','bg-secondary text-secondary-foreground'],
    'in_progress' => ['In Progress','border border-blue-500 text-blue-400'],
    'resolved' => ['Resolved','bg-green-500/10 text-green-400'],
    default => [$status,'bg-secondary text-secondary-foreground'],
  };

  $prio = fn($p) => match($p){
    'high' => ['High','bg-red-500/15 text-red-400'],
    'medium' => ['Medium','bg-orange-500/15 text-orange-400'],
    'low' => ['Low','bg-green-500/10 text-green-400'],
    default => [$p,'bg-secondary text-secondary-foreground'],
  };
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Support</h1>
      <p class="text-muted-foreground">Manage support tickets and user issues</p>
    </div>
    <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
      <i data-lucide="plus" class="h-4 w-4 mr-2 inline"></i>
      New Ticket
    </button>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/50">
          <tr>
            <th class="text-left p-4 font-medium">Subject</th>
            <th class="text-left p-4 font-medium">User</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Priority</th>
            <th class="text-left p-4 font-medium">Date</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tickets as $t)
            @php [$st,$stCls] = $badge($t['status']); @endphp
            @php [$pr,$prCls] = $prio($t['priority']); @endphp
            <tr class="border-b last:border-0" data-testid="ticket-{{ $t['id'] }}">
              <td class="p-4 font-medium">{{ $t['subject'] }}</td>
              <td class="p-4">
                <div class="text-sm">{{ $t['user'] }}</div>
                <div class="text-xs text-muted-foreground">{{ $t['email'] }}</div>
              </td>
              <td class="p-4">
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $stCls }}">{{ $st }}</span>
              </td>
              <td class="p-4">
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $prCls }}">{{ $pr }}</span>
              </td>
              <td class="p-4 text-sm text-muted-foreground">{{ $t['date'] }}</td>
              <td class="p-4">
                <div class="flex gap-2">
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">View</button>
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Assign</button>
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">Close</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
