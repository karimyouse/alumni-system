@extends('layouts.dashboard')

@php
  $title = __('Content Management');

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'User Management','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Content Management','href'=>'/admin/content','icon'=>'file-text'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
    ['label'=>'System Settings','href'=>'/admin/settings','icon'=>'settings'],
    ['label'=>'Support Center','href'=>'/admin/support','icon'=>'help-circle'],
  ];

  $tab = $tab ?? request('tab','announcements');
  $status = $status ?? request('status','all');

  $tabCounts = $tabCounts ?? ['announcements'=>0,'success_stories'=>0,'workshops'=>0,'scholarships'=>0];
  $items = $items ?? collect([]);
  $statusCol = $statusCol ?? null;

  $tabs = [
    ['key'=>'announcements','label'=>'Announcements','count'=>$tabCounts['announcements'] ?? 0,'icon'=>'megaphone'],
    ['key'=>'success_stories','label'=>'Success Stories','count'=>$tabCounts['success_stories'] ?? 0,'icon'=>'award'],
    ['key'=>'workshops','label'=>'Workshops','count'=>$tabCounts['workshops'] ?? 0,'icon'=>'calendar-days'],
    ['key'=>'scholarships','label'=>'Scholarships','count'=>$tabCounts['scholarships'] ?? 0,'icon'=>'graduation-cap'],
  ];

  $statusTabs = [
    ['key'=>'all','label'=>'All'],
    ['key'=>'pending','label'=>'Pending'],
    ['key'=>'approved','label'=>'Approved'],
    ['key'=>'rejected','label'=>'Rejected'],
  ];

  $statusPill = function ($st) {
    $st = strtolower((string)$st);
    return match($st) {
      'approved' => 'bg-green-500/15 text-green-400',
      'rejected' => 'bg-red-500/15 text-red-400',
      'pending'  => 'bg-yellow-500/15 text-yellow-400',
      default    => 'bg-secondary text-secondary-foreground',
    };
  };
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Content Management</h1>
    <p class="text-sm text-muted-foreground">Review and approve content submissions</p>
  </div>


  <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-center">
    @foreach($tabs as $t)
      <a href="{{ route('admin.content', ['tab'=>$t['key'], 'status'=>$status]) }}"
         class="rounded-md px-3 py-2 text-sm border border-border transition inline-flex items-center justify-center gap-2
         {{ $tab === $t['key'] ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
        <i data-lucide="{{ $t['icon'] }}" class="h-4 w-4"></i>
        {{ $t['label'] }} ({{ $t['count'] }})
      </a>
    @endforeach
  </div>


  <div class="flex flex-wrap items-center gap-2">
    <div class="w-full text-sm text-muted-foreground sm:w-auto">Status:</div>
    @foreach($statusTabs as $s)
      <a href="{{ route('admin.content', ['tab'=>$tab, 'status'=>$s['key']]) }}"
         class="inline-flex flex-1 items-center justify-center rounded-md px-3 py-2 text-sm border border-border transition sm:flex-none
         {{ $status === $s['key'] ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}">
        {{ $s['label'] }}
      </a>
    @endforeach
  </div>


  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="p-4 border-b border-border sm:p-6">
      <div class="text-lg font-semibold">Items</div>
      <div class="text-sm text-muted-foreground">View details + approve/reject with admin note</div>
    </div>

    <div class="divide-y divide-border">
      @forelse($items as $it)
        @php
          $itemTitle =
            $it->title
            ?? $it->subject
            ?? $it->name
            ?? 'Item';

          $meta = '';
          if ($tab === 'workshops') {
            $meta = 'Date: ' . ($it->date ?? '—');
          } elseif ($tab === 'scholarships') {
            $meta = 'Deadline: ' . ($it->deadline ?? '—');
          } else {
            $meta = 'Created: ' . (isset($it->created_at) ? \Carbon\Carbon::parse($it->created_at)->format('M d, Y') : '—');
          }


          $st = ($statusCol && isset($it->{$statusCol})) ? ($it->{$statusCol} ?? '—') : '—';
          $stClass = $statusPill($st);

          $viewDesc =
            $it->description
            ?? $it->content
            ?? $it->body
            ?? $it->story
            ?? $it->requirements
            ?? null;
        @endphp

        <div class="p-4 flex flex-col gap-4 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
              <i data-lucide="{{ $tab === 'workshops' ? 'calendar-days' : ($tab === 'scholarships' ? 'graduation-cap' : ($tab === 'announcements' ? 'megaphone' : 'award')) }}"
                 class="h-5 w-5"></i>
            </div>

            <div class="min-w-0">
              <div class="font-semibold truncate sm:whitespace-normal sm:break-words">{{ $itemTitle }}</div>
              <div class="text-xs text-muted-foreground truncate sm:whitespace-normal sm:break-words">{{ $meta }}</div>
            </div>
          </div>

          <div class="grid w-full grid-cols-[auto_1fr_auto_auto] gap-2 sm:flex sm:w-auto sm:flex-wrap sm:items-center sm:justify-end">
            <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ strtolower((string)$st) }}</span>


            <button type="button"
                    class="h-9 rounded-md border border-border px-3 text-sm hover:bg-accent/50 inline-flex items-center justify-center gap-2"
                    data-title="{{ e($itemTitle) }}"
                    data-meta="{{ e($meta) }}"
                    data-status="{{ e(strtolower((string)$st)) }}"
                    data-desc="{{ e((string)($viewDesc ?? '')) }}"
                    onclick="openViewModal(this)">
              <i data-lucide="eye" class="h-4 w-4"></i>
              View
            </button>


            <button type="button"
                    class="h-9 w-9 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent/50"
                    title="Reject"
                    data-reject-url="{{ route('admin.content.reject', ['type'=>$tab, 'id'=>$it->id]) }}"
                    data-title="{{ e($itemTitle) }}"
                    onclick="openRejectModal(this)">
              <i data-lucide="x" class="h-4 w-4 text-red-400"></i>
            </button>


            <form method="POST" action="{{ route('admin.content.approve', ['type'=>$tab, 'id'=>$it->id]) }}">
              @csrf
              <button type="submit"
                      class="h-9 w-9 inline-flex items-center justify-center rounded-md bg-green-500/15 hover:bg-green-500/25"
                      title="Approve">
                <i data-lucide="check" class="h-4 w-4 text-green-400"></i>
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No items found.</div>
      @endforelse
    </div>
  </div>


  @if(method_exists($items, 'links'))
    <div class="pt-2">
      {{ $items->links() }}
    </div>
  @endif

</div>


<div id="viewModal" class="hidden">
  <div class="fixed inset-0 bg-black/60 z-[9998]" onclick="closeAllModals()"></div>

  <div class="fixed z-[9999] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[92vw] max-w-2xl rounded-2xl border border-border bg-card shadow-2xl">
    <div class="p-4 border-b border-border flex items-start justify-between gap-3 sm:p-6">
      <div class="min-w-0">
        <div class="text-lg font-semibold" id="vmTitle">Item</div>
        <div class="text-xs text-muted-foreground" id="vmMeta">—</div>
      </div>

      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
              onclick="closeAllModals()">
        <i data-lucide="x" class="h-4 w-4"></i>
      </button>
    </div>

    <div class="p-4 space-y-4 sm:p-6">
      <div class="flex items-center gap-2">
        <span class="text-xs rounded-full px-2 py-1" id="vmStatus">—</span>
      </div>

      <div class="rounded-xl border border-border p-4">
        <div class="text-sm font-semibold mb-2">Details</div>
        <div class="text-sm text-muted-foreground whitespace-pre-line" id="vmDesc">—</div>
      </div>

      <div class="flex justify-end">
        <button type="button"
                class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50"
                onclick="closeAllModals()">
          Close
        </button>
      </div>
    </div>
  </div>
</div>


<div id="rejectModal" class="hidden">
  <div class="fixed inset-0 bg-black/60 z-[9998]" onclick="closeAllModals()"></div>

  <div class="fixed z-[9999] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[92vw] max-w-xl rounded-2xl border border-border bg-card shadow-2xl">
    <div class="p-4 border-b border-border flex items-start justify-between gap-3 sm:p-6">
      <div class="min-w-0">
        <div class="text-lg font-semibold">Reject Item</div>
        <div class="text-xs text-muted-foreground" id="rmTitle">—</div>
      </div>

      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
              onclick="closeAllModals()">
        <i data-lucide="x" class="h-4 w-4"></i>
      </button>
    </div>

    <form method="POST" id="rejectForm" class="p-4 space-y-4 sm:p-6">
      @csrf
      <div>
        <label class="text-sm font-medium">Admin Note (optional)</label>
        <textarea id="rmNote" name="admin_note" rows="4"
                  class="mt-2 w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                  placeholder="Write the reason for rejection..."></textarea>
      </div>

      <div class="grid grid-cols-1 gap-2 sm:flex sm:items-center sm:justify-end">
        <button type="button"
                class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50"
                onclick="closeAllModals()">
          Cancel
        </button>

        <button type="submit"
                class="rounded-md bg-red-500/90 px-4 py-2 text-sm text-white hover:opacity-90">
          Reject
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function closeAllModals() {
    const v = document.getElementById('viewModal');
    const r = document.getElementById('rejectModal');
    if (v) v.classList.add('hidden');
    if (r) r.classList.add('hidden');
  }

  function pillClass(status) {
    status = (status || '').toLowerCase();
    if (status === 'approved') return 'bg-green-500/15 text-green-400';
    if (status === 'rejected') return 'bg-red-500/15 text-red-400';
    if (status === 'pending')  return 'bg-yellow-500/15 text-yellow-400';
    return 'bg-secondary text-secondary-foreground';
  }

  function openViewModal(btn) {

    closeAllModals();

    const title = btn.dataset.title || 'Item';
    const meta  = btn.dataset.meta || '—';
    const status = btn.dataset.status || '—';
    const desc  = btn.dataset.desc || '—';

    document.getElementById('vmTitle').innerText = title;
    document.getElementById('vmMeta').innerText = meta;

    const stEl = document.getElementById('vmStatus');
    stEl.innerText = status;
    stEl.className = 'text-xs rounded-full px-2 py-1 ' + pillClass(status);

    document.getElementById('vmDesc').innerText = (desc && desc.trim()) ? desc : '—';

    document.getElementById('viewModal').classList.remove('hidden');
  }

  function openRejectModal(btn) {

    closeAllModals();

    const title = btn.dataset.title || 'Item';
    const url = btn.dataset.rejectUrl;

    document.getElementById('rmTitle').innerText = title;

    const form = document.getElementById('rejectForm');
    if (url) form.setAttribute('action', url);


    const note = document.getElementById('rmNote');
    if (note) note.value = '';

    document.getElementById('rejectModal').classList.remove('hidden');
  }


  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAllModals();
  });
</script>
@endsection
