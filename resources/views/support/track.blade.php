@extends('layouts.app')

@php
  $title = __('Track Support Request');
@endphp

@section('content')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center">
  <div class="absolute inset-0 bg-gradient-to-br from-background via-background to-background"></div>
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.06)_0%,rgba(0,0,0,0)_55%)]"></div>

  <div class="absolute top-4 left-4 flex items-center gap-2 text-sm text-muted-foreground">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 hover:text-foreground transition">
      <i data-lucide="chevron-left" class="h-4 w-4"></i>
      <span>{{ __("Back to Login") }}</span>
    </a>
  </div>

  <div class="relative z-10 w-full max-w-3xl rounded-xl border border-border bg-card/80 backdrop-blur p-6 shadow-xl">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-lg bg-primary text-primary-foreground flex items-center justify-center mb-3">
        <i data-lucide="search" class="w-6 h-6"></i>
      </div>
      <h1 class="text-2xl font-bold">{{ __("Track Support Request") }}</h1>
      <p class="text-sm text-muted-foreground">{{ __("Enter your tracking code and email to view the ticket") }}</p>
    </div>

    @if(!empty($notFound))
      <div class="mb-5 rounded-xl border border-destructive/30 bg-destructive/10 p-4 text-sm">
        <div class="font-semibold text-destructive mb-1 inline-flex items-center gap-2">
          <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          {{ __("Not found") }}
        </div>
        <div class="text-destructive/90">
          {{ __("We couldn't find a ticket for this tracking code and email.") }}
        </div>
      </div>
    @endif

    <form method="GET" action="{{ route('support.track.show') }}" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">{{ __("Tracking Code") }}</label>
          <input name="code" value="{{ old('code', $code ?? '') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="SUP-XXXXXXX">
        </div>

        <div>
          <label class="text-sm font-medium">{{ __("Email") }}</label>
          <input name="email" type="email" value="{{ old('email', $email ?? '') }}" required
                 class="mt-1 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                 placeholder="you@example.com">
        </div>
      </div>

      <button type="submit"
              class="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground font-medium hover:opacity-90 transition">
        {{ __("Track") }}
      </button>
    </form>

    @if(!empty($ticket))
      @php
        $st = $ticket->status ?? 'open';
        $stClass = match($st) {
          'resolved' => 'bg-green-500/15 text-green-400',
          'in_progress' => 'bg-blue-500/15 text-blue-400',
          default => 'bg-orange-500/15 text-orange-400',
        };
      @endphp

      <div class="mt-6 space-y-4">
        <div class="rounded-xl border border-border p-4">
          <div class="flex items-center justify-between gap-2">
            <div class="text-sm font-semibold">Ticket #{{ $ticket->id }}</div>
            <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ str_replace('_',' ', $st) }}</span>
          </div>

          <div class="text-xs text-muted-foreground mt-2">
            {{ __("Tracking Code:") }} <span class="font-mono text-foreground">{{ $ticket->tracking_code }}</span>
          </div>

          <div class="text-xs text-muted-foreground mt-1">
            {{ __("Created:") }} {{ $ticket->created_at?->format('M d, Y h:i A') ?? '—' }}
          </div>
        </div>

        <div class="rounded-xl border border-border p-4">
          <div class="text-sm font-semibold mb-1">{{ $ticket->title ?? $ticket->subject ?? __('Request') }}</div>
          <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $ticket->message }}</div>
        </div>

        @php
  $linkifyReply = function (?string $text) {
      $escaped = e((string) $text);

      $linked = preg_replace(
          '/(https?:\/\/[^\s<]+)/u',
          '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-primary underline break-all hover:opacity-80">$1</a>',
          $escaped
      );

      return nl2br($linked ?? $escaped);
  };
@endphp

<div class="rounded-xl border border-border p-4 bg-accent/10">
  <div class="text-sm font-semibold mb-1">{{ __("Admin Reply") }}</div>

  @if(!empty($ticket->admin_reply))
    <div class="text-sm text-muted-foreground whitespace-pre-line break-words">
      {!! $linkifyReply($ticket->admin_reply) !!}
    </div>
    <div class="text-xs text-muted-foreground mt-2">
      {{ __("Replied at:") }}
      {{ $ticket->admin_replied_at?->format('M d, Y h:i A') ?? ($ticket->updated_at?->format('M d, Y h:i A') ?? '—') }}
    </div>
  @else
    <div class="text-sm text-muted-foreground">{{ __("No reply yet. Please check again later.") }}</div>
  @endif
</div>
      </div>
    @endif

  </div>
</div>
@endsection
