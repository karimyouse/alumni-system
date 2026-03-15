@extends('layouts.dashboard')

@php
  $title = __('My Support Tickets');


  $role = auth()->user()?->role ?? 'alumni';
  $home = match($role) {
    'admin', 'super_admin' => '/admin',
    'college' => '/college',
    'company' => '/company',
    default => '/alumni',
  };

  $nav = [
    ['label'=>'Overview','href'=>$home,'icon'=>'layout-dashboard'],
    ['label'=>'My Tickets','href'=>route('support.tickets'),'icon'=>'help-circle'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">My Support Tickets</h1>
    <p class="text-sm text-muted-foreground">View your requests and admin replies</p>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="grid grid-cols-12 gap-4 px-6 py-3 border-b border-border text-xs text-muted-foreground">
      <div class="col-span-6">Title</div>
      <div class="col-span-2">Status</div>
      <div class="col-span-2">Priority</div>
      <div class="col-span-2 text-right">Action</div>
    </div>

    <div class="divide-y divide-border">
      @forelse($tickets as $t)
        @php
          $st = $t->status ?? 'open';
          $stClass = match($st) {
            'resolved' => 'bg-green-500/15 text-green-400',
            'in_progress' => 'bg-blue-500/15 text-blue-400',
            default => 'bg-muted text-foreground',
          };

          $prio = $t->priority ?? 'medium';
          $prioClass = match($prio) {
            'high' => 'bg-red-500/15 text-red-400',
            'low' => 'bg-secondary text-secondary-foreground',
            default => 'bg-secondary text-secondary-foreground',
          };

          $hasReply = !empty($t->admin_reply);
        @endphp

        <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center">
          <div class="col-span-6 min-w-0">
            <div class="text-sm font-medium truncate">
              {{ $t->title ?? $t->subject ?? 'Support Ticket' }}
              @if($hasReply)
                <span class="ml-2 text-[11px] rounded-full px-2 py-0.5 bg-primary/10 text-primary align-middle">replied</span>
              @endif
            </div>
            <div class="text-xs text-muted-foreground">
              {{ $t->created_at ? $t->created_at->format('M d, Y') : '—' }}
            </div>
          </div>

          <div class="col-span-2">
            <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ str_replace('_',' ', $st) }}</span>
          </div>

          <div class="col-span-2">
            <span class="text-xs rounded-full px-2 py-1 {{ $prioClass }}">{{ $prio }}</span>
          </div>

          <div class="col-span-2 text-right">
            <a href="{{ route('support.tickets.show', $t) }}"
               class="inline-flex items-center gap-2 rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              <i data-lucide="eye" class="h-4 w-4"></i>
              View
            </a>
          </div>
        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No tickets found.</div>
      @endforelse
    </div>
  </div>

  @if(method_exists($tickets, 'links'))
    <div>{{ $tickets->links() }}</div>
  @endif

</div>
@endsectionت
