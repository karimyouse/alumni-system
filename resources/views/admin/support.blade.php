@extends('layouts.dashboard')

@php
  use Illuminate\Support\Str;

  $title = 'Support Center';

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $tickets = $tickets ?? collect([]);
  $counts  = $counts ?? ['all'=>0,'open'=>0,'in_progress'=>0,'resolved'=>0];
  $status  = $status ?? request('status','all');

  $tabs = [
    ['key'=>'all', 'label'=>'All', 'count'=>$counts['all'] ?? 0],
    ['key'=>'open', 'label'=>'Open', 'count'=>$counts['open'] ?? 0],
    ['key'=>'in_progress', 'label'=>'In Progress', 'count'=>$counts['in_progress'] ?? 0],
    ['key'=>'resolved', 'label'=>'Resolved', 'count'=>$counts['resolved'] ?? 0],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Support Center</h1>
    <p class="text-sm text-muted-foreground">Handle user support requests</p>
  </div>

  {{-- Counters --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-orange-500/15 text-orange-400 flex items-center justify-center">
          <i data-lucide="clock" class="h-4 w-4"></i>
        </div>
        <div>
          <div class="text-2xl font-bold">{{ $counts['open'] ?? 0 }}</div>
          <div class="text-sm text-muted-foreground">Open Tickets</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-500/15 text-blue-400 flex items-center justify-center">
          <i data-lucide="loader" class="h-4 w-4"></i>
        </div>
        <div>
          <div class="text-2xl font-bold">{{ $counts['in_progress'] ?? 0 }}</div>
          <div class="text-sm text-muted-foreground">In Progress</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-500/15 text-green-400 flex items-center justify-center">
          <i data-lucide="check-circle-2" class="h-4 w-4"></i>
        </div>
        <div>
          <div class="text-2xl font-bold">{{ $counts['resolved'] ?? 0 }}</div>
          <div class="text-sm text-muted-foreground">Resolved</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="flex items-center gap-2">
    @foreach($tabs as $t)
      <a href="{{ route('admin.support', ['status'=>$t['key']]) }}"
         class="rounded-md px-3 py-1.5 text-sm border border-border transition
         {{ $status === $t['key'] ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
        {{ $t['label'] }} ({{ $t['count'] }})
      </a>
    @endforeach
  </div>

  {{-- Tickets --}}
  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="p-6 border-b border-border">
      <div class="text-lg font-semibold">Tickets</div>
    </div>

    <div class="divide-y divide-border">
      @forelse($tickets as $ticket)
        @php
          $name = $ticket->name ?? $ticket->user?->name ?? 'User';
          $email = $ticket->email ?? $ticket->user?->email ?? '—';
          $titleText = $ticket->title ?? $ticket->subject ?? 'Support Ticket';
          $dateText = $ticket->created_at ? $ticket->created_at->format('M d, Y') : '—';

          $tracking = $ticket->tracking_code ?? null;
          $roleTxt = $ticket->role ?? null;
          $identifierTxt = $ticket->identifier ?? null;

          $st = $ticket->status ?? 'open';
          $stClass = match($st) {
            'resolved' => 'bg-green-500/15 text-green-400',
            'in_progress' => 'bg-blue-500/15 text-blue-400',
            default => 'bg-muted text-foreground',
          };

          $prio = $ticket->priority ?? 'medium';
          $prioClass = match($prio) {
            'high' => 'bg-red-500/15 text-red-400',
            'low' => 'bg-secondary text-secondary-foreground',
            default => 'bg-secondary text-secondary-foreground',
          };

          $initials = collect(explode(' ', $name))->map(fn($n)=>mb_substr($n,0,1))->join('');
          $initials = $initials ?: 'U';

          $preview = !empty($ticket->message) ? Str::limit($ticket->message, 90) : null;
          $assigned = $ticket->admin?->name ?? null;
        @endphp

        {{-- ✅ IMPORTANT: wrapper per ticket (fix peer bleeding) --}}
        <div class="relative">

          {{-- Modal toggle --}}
          <input type="checkbox" id="ticket-modal-{{ $ticket->id }}" class="peer hidden ticket-modal">

          {{-- Anchor for notifications --}}
          <div id="ticket-{{ $ticket->id }}" class="p-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                {{ $initials }}
              </div>

              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[11px] px-2 py-1 rounded-full border border-border text-muted-foreground">
                    #{{ $ticket->id }}
                  </span>

                  <div class="font-semibold truncate">{{ $titleText }}</div>

                  <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ str_replace('_',' ', $st) }}</span>
                  <span class="text-xs rounded-full px-2 py-1 {{ $prioClass }}">{{ $prio }}</span>

                  @if($tracking)
                    <span class="text-[11px] px-2 py-1 rounded-full border border-border text-muted-foreground font-mono">
                      {{ $tracking }}
                    </span>
                  @endif
                </div>

                <div class="text-xs text-muted-foreground truncate">
                  {{ $name }} ({{ $email }}) • {{ $dateText }}
                </div>

                @if($roleTxt || $identifierTxt)
                  <div class="text-xs text-muted-foreground mt-1 truncate">
                    @if($roleTxt) Role: <span class="text-foreground">{{ $roleTxt }}</span>@endif
                    @if($roleTxt && $identifierTxt) • @endif
                    @if($identifierTxt) Identifier: <span class="text-foreground">{{ $identifierTxt }}</span>@endif
                  </div>
                @endif

                @if($preview)
                  <div class="text-xs text-muted-foreground mt-1 truncate">{{ $preview }}</div>
                @endif

                @if($assigned)
                  <div class="text-xs text-muted-foreground mt-1">
                    Assigned: <span class="text-foreground">{{ $assigned }}</span>
                  </div>
                @endif
              </div>
            </div>

            <div class="flex items-center gap-2">
              <form method="POST" action="{{ route('admin.support.status', $ticket) }}" class="flex items-center gap-2">
                @csrf
                <select name="status" class="h-9 rounded-md border border-input bg-background/60 px-2 text-sm">
                  <option value="open" {{ $st === 'open' ? 'selected' : '' }}>Open</option>
                  <option value="in_progress" {{ $st === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                  <option value="resolved" {{ $st === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>

                <select name="priority" class="h-9 rounded-md border border-input bg-background/60 px-2 text-sm">
                  <option value="low" {{ $prio === 'low' ? 'selected' : '' }}>Low</option>
                  <option value="medium" {{ $prio === 'medium' ? 'selected' : '' }}>Medium</option>
                  <option value="high" {{ $prio === 'high' ? 'selected' : '' }}>High</option>
                </select>

                <button type="submit" class="h-9 rounded-md border border-border px-3 text-sm hover:bg-accent/50">
                  Update
                </button>
              </form>

              <label for="ticket-modal-{{ $ticket->id }}"
                     class="cursor-pointer rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50 inline-flex items-center gap-2">
                <i data-lucide="message-square" class="h-4 w-4"></i>
                Respond
              </label>
            </div>
          </div>

          {{-- ✅ Modal (isolated to this ticket only) --}}
          <div class="hidden peer-checked:block">
            <div class="fixed inset-0 z-50">
              <label for="ticket-modal-{{ $ticket->id }}" class="absolute inset-0 bg-black/60 cursor-pointer"></label>

              <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                          w-[92vw] max-w-2xl max-h-[85vh]
                          rounded-2xl border border-border bg-card shadow-2xl
                          overflow-hidden flex flex-col z-[60]">

                <div class="p-6 border-b border-border flex items-start justify-between gap-4 flex-shrink-0">
                  <div class="min-w-0">
                    <div class="text-lg font-semibold">Ticket #{{ $ticket->id }}</div>
                    <div class="text-xs text-muted-foreground truncate">{{ $name }} • {{ $email }}</div>

                    @if($tracking)
                      <div class="text-xs text-muted-foreground mt-1 flex items-center gap-2">
                        Tracking:
                        <span class="font-mono text-foreground">{{ $tracking }}</span>
                        <button type="button" class="text-xs text-primary hover:underline"
                                onclick="copyText('{{ $tracking }}')">Copy</button>
                      </div>
                    @endif

                    @if($roleTxt || $identifierTxt)
                      <div class="text-xs text-muted-foreground mt-1 truncate">
                        @if($roleTxt) Role: <span class="text-foreground">{{ $roleTxt }}</span>@endif
                        @if($roleTxt && $identifierTxt) • @endif
                        @if($identifierTxt) Identifier: <span class="text-foreground">{{ $identifierTxt }}</span>@endif
                      </div>
                    @endif

                    @if($assigned)
                      <div class="text-xs text-muted-foreground mt-1">
                        Assigned: <span class="text-foreground">{{ $assigned }}</span>
                      </div>
                    @endif
                  </div>

                  <label for="ticket-modal-{{ $ticket->id }}"
                         class="cursor-pointer h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50 flex-shrink-0">
                    <i data-lucide="x" class="h-4 w-4"></i>
                  </label>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto">
                  <div class="rounded-xl border border-border p-4">
                    <div class="text-sm font-semibold mb-1">{{ $titleText }}</div>
                    <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $ticket->message }}</div>
                  </div>

                  @if(!empty($ticket->admin_reply))
                    <div class="rounded-xl border border-border p-4 bg-accent/20">
                      <div class="text-sm font-semibold mb-1">Admin Reply</div>
                      <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $ticket->admin_reply }}</div>
                    </div>
                  @endif

                  <form method="POST" action="{{ route('admin.support.respond', $ticket) }}" class="space-y-3">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <div>
                        <label class="text-sm font-medium">Status</label>
                        <select name="status" class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
                          <option value="open" {{ $st === 'open' ? 'selected' : '' }}>Open</option>
                          <option value="in_progress" {{ $st === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                          <option value="resolved" {{ $st === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                      </div>

                      <div>
                        <label class="text-sm font-medium">Priority</label>
                        <select name="priority" class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
                          <option value="low" {{ $prio === 'low' ? 'selected' : '' }}>Low</option>
                          <option value="medium" {{ $prio === 'medium' ? 'selected' : '' }}>Medium</option>
                          <option value="high" {{ $prio === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                      </div>
                    </div>

                    <div>
                      <label class="text-sm font-medium">Reply</label>
                      <textarea name="admin_reply" rows="5"
                                class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                placeholder="Write your reply..."></textarea>
                      <div class="text-xs text-muted-foreground mt-1">Tip: choose “Resolved” to close the ticket.</div>

                      @if($ticket->user && !empty($ticket->user->email))
                        <label class="mt-3 flex items-center gap-2 text-xs text-muted-foreground">
                          <input type="checkbox" name="include_reset_link" value="1" class="h-4 w-4 rounded border-border bg-background/60">
                          Include password reset link (user will set a new password)
                        </label>
                      @endif
                    </div>

                    <div class="flex items-center justify-end gap-2">
                      <label for="ticket-modal-{{ $ticket->id }}"
                             class="cursor-pointer rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
                        Cancel
                      </label>

                      <button type="submit"
                              class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
                        Save
                      </button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>

        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No tickets found.</div>
      @endforelse
    </div>
  </div>

  {{-- Pagination --}}
  @if(method_exists($tickets, 'links'))
    <div class="pt-2">{{ $tickets->links() }}</div>
  @endif

</div>

<script>
  // ✅ Ensure only one modal open at a time
  (function () {
    const cbs = Array.from(document.querySelectorAll('.ticket-modal'));
    cbs.forEach(cb => {
      cb.addEventListener('change', () => {
        if (!cb.checked) return;
        cbs.forEach(other => { if (other !== cb) other.checked = false; });
      });
    });

    // ✅ Auto-open when coming from #ticket-123
    const hash = window.location.hash || '';
    if (hash.startsWith('#ticket-')) {
      const id = hash.replace('#ticket-', '').trim();
      const row = document.querySelector(hash);

      if (row) {
        row.classList.add('ring-2','ring-primary','ring-offset-2','ring-offset-background','rounded-xl');
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }

      const cb = document.getElementById('ticket-modal-' + id);
      if (cb) {
        cbs.forEach(other => other.checked = false);
        cb.checked = true;
      }
    }
  })();

  function copyText(text) {
    if (!text) return;

    const toast = (msg) => {
      const el = document.createElement('div');
      el.className = 'fixed bottom-6 right-6 z-[99999] rounded-xl border border-border bg-card shadow-xl px-4 py-3 text-sm';
      el.innerText = msg;
      document.body.appendChild(el);
      setTimeout(() => el.remove(), 1500);
    };

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(() => toast('Copied')).catch(() => {});
      return;
    }

    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); toast('Copied'); } catch (e) {}
    ta.remove();
  }
</script>
@endsection
