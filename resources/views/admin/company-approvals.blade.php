@extends('layouts.dashboard')

@php
  $title='Company Approvals';
  $role='Admin';

  $nav = [
    ['label'=>'Overview','href'=>'/admin','icon'=>'layout-dashboard'],
    ['label'=>'Company Approvals','href'=>'/admin/company-approvals','icon'=>'check-circle', 'badge'=>$counts['pending'] ?? 0],
    ['label'=>'Users','href'=>'/admin/users','icon'=>'users'],
    ['label'=>'Reports','href'=>'/admin/reports','icon'=>'bar-chart-3'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Company Approvals</h1>
      <p class="text-sm text-muted-foreground">Approve or reject company registration requests</p>
    </div>
  </div>

  <div class="flex flex-wrap gap-2">
    <a class="rounded-md border border-border px-3 py-2 text-sm {{ $status==='pending'?'bg-accent':'' }}"
       href="{{ route('admin.companyApprovals', ['status'=>'pending']) }}">Pending ({{ $counts['pending'] }})</a>

    <a class="rounded-md border border-border px-3 py-2 text-sm {{ $status==='approved'?'bg-accent':'' }}"
       href="{{ route('admin.companyApprovals', ['status'=>'approved']) }}">Approved ({{ $counts['approved'] }})</a>

    <a class="rounded-md border border-border px-3 py-2 text-sm {{ $status==='rejected'?'bg-accent':'' }}"
       href="{{ route('admin.companyApprovals', ['status'=>'rejected']) }}">Rejected ({{ $counts['rejected'] }})</a>

    <a class="rounded-md border border-border px-3 py-2 text-sm {{ $status==='all'?'bg-accent':'' }}"
       href="{{ route('admin.companyApprovals', ['status'=>'all']) }}">All ({{ $counts['all'] }})</a>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Company</th>
            <th class="text-left p-4 font-medium">Email</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($profiles as $p)
            @php
              $pill = match($p->status){
                'pending' => ['Pending','bg-orange-500/15 text-orange-400'],
                'approved' => ['Approved','bg-green-500/15 text-green-400'],
                'rejected' => ['Rejected','bg-red-500/15 text-red-400'],
                default => [ucfirst($p->status),'bg-secondary text-secondary-foreground'],
              };
            @endphp

            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $p->company_name }}</div>
                <div class="text-xs text-muted-foreground">{{ $p->industry ?? '—' }} • {{ $p->location ?? '—' }}</div>
              </td>

              <td class="p-4 text-sm text-muted-foreground">{{ $p->user?->email }}</td>

              <td class="p-4">
                <span class="text-xs rounded-full px-2 py-1 {{ $pill[1] }}">{{ $pill[0] }}</span>
              </td>

              <td class="p-4">
                <div class="flex flex-col gap-2">
                  @if($p->status !== 'approved')
                    <form method="POST" action="{{ route('admin.companyApprovals.approve', $p) }}">
                      @csrf
                      <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">
                        Approve
                      </button>
                    </form>
                  @endif

                  @if($p->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.companyApprovals.reject', $p) }}" class="space-y-2">
                      @csrf
                      <input name="admin_note"
                             class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                             placeholder="Reject reason (optional)" />
                      <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                        Reject
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="4">No records.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    {{ $profiles->links() }}
  </div>

</div>
@endsection
