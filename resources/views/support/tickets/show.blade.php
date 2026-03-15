@extends('layouts.dashboard')

@php
  $title = __('Ticket Details');


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

  $st = $ticket->status ?? 'open';
  $stClass = match($st) {
    'resolved' => 'bg-green-500/15 text-green-400',
    'in_progress' => 'bg-blue-500/15 text-blue-400',
    default => 'bg-muted text-foreground',
  };
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Ticket #{{ $ticket->id }}</h1>
      <p class="text-sm text-muted-foreground">{{ $ticket->created_at ? $ticket->created_at->format('M d, Y H:i') : '' }}</p>
    </div>

    <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ str_replace('_',' ', $st) }}</span>
  </div>

  <div class="rounded-xl border border-border bg-card p-6">
    <div class="text-lg font-semibold mb-2">{{ $ticket->title ?? $ticket->subject ?? 'Support Ticket' }}</div>
    <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $ticket->message }}</div>
  </div>

  <div class="rounded-xl border border-border bg-card p-6">
    <div class="text-lg font-semibold mb-2">Admin Reply</div>

    @if(!empty($ticket->admin_reply))
      <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $ticket->admin_reply }}</div>
      @if($ticket->resolved_at)
        <div class="text-xs text-muted-foreground mt-3">Resolved at: {{ $ticket->resolved_at->format('M d, Y H:i') }}</div>
      @endif
    @else
      <div class="text-sm text-muted-foreground">No reply yet.</div>
    @endif
  </div>

</div>
@endsection
