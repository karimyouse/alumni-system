@extends('layouts.dashboard')

@php
  $title = __('Applications');
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>$applicationBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
  ];

  $tabs = [
    ['key' => 'all', 'label' => 'All'],
    ['key' => 'pending', 'label' => 'Pending'],
    ['key' => 'reviewed', 'label' => 'Under Review'],
    ['key' => 'accepted', 'label' => 'Accepted'],
    ['key' => 'rejected', 'label' => 'Rejected'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Applications</h1>
    <p class="text-sm text-muted-foreground">Review and manage alumni applications</p>
  </div>

  <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
    @foreach($tabs as $t)
      @php
        $active = ($tab ?? 'all') === $t['key'];
        $count = $counts[$t['key']] ?? 0;
      @endphp
      <a href="{{ route('company.applications', ['tab' => $t['key']]) }}"
         class="inline-flex items-center justify-center gap-2 rounded-md border border-border px-3 py-2 text-sm
         {{ $active ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/30 hover:text-foreground' }}">
        <span>{{ $t['label'] }}</span>
        <span class="text-xs rounded-full bg-secondary px-2 py-0.5 text-secondary-foreground">{{ $count }}</span>
      </a>
    @endforeach
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="hidden overflow-auto md:block">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Applicant</th>
            <th class="text-left p-4 font-medium">Job</th>
            <th class="text-left p-4 font-medium">Applied</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Update</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $it)
            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $it['applicant_name'] }}</div>
                <div class="text-xs text-muted-foreground">
                  {{ $it['academic_id'] }} • {{ $it['applicant_email'] }}
                </div>
              </td>

              <td class="p-4">
                <div class="font-semibold">{{ $it['job_title'] }}</div>
                <div class="text-xs text-muted-foreground">{{ $it['company_name'] }}</div>
              </td>

              <td class="p-4 text-sm text-muted-foreground">{{ $it['applied_at'] }}</td>

              <td class="p-4">
                <span class="text-xs rounded-full px-2 py-1 {{ $it['status_class'] }}">
                  {{ $it['status_label'] }}
                </span>
              </td>

              <td class="p-4">
                <form method="POST" action="{{ route('company.applications.status', $it['id']) }}" class="flex items-center gap-2">
                  @csrf
                  <select name="status" class="rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
                    <option value="pending"  {{ $it['status']==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ $it['status']==='reviewed' ? 'selected' : '' }}>Under Review</option>
                    <option value="accepted" {{ $it['status']==='accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ $it['status']==='rejected' ? 'selected' : '' }}>Rejected</option>
                  </select>

                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    Update
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="5">No applications found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="divide-y divide-border md:hidden">
      @forelse($items as $it)
        <div class="p-4 space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
              <i data-lucide="file-text" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="font-semibold leading-snug break-words">{{ $it['applicant_name'] }}</div>
              <div class="text-xs text-muted-foreground mt-1 break-words">
                {{ $it['academic_id'] }} • {{ $it['applicant_email'] }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-2 text-sm">
            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Job</span>
              <span class="text-right font-medium break-words">{{ $it['job_title'] }}</span>
            </div>

            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Company</span>
              <span class="text-right break-words">{{ $it['company_name'] }}</span>
            </div>

            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Applied</span>
              <span class="text-right break-words">{{ $it['applied_at'] }}</span>
            </div>

            <div class="flex items-center justify-between gap-3">
              <span class="text-muted-foreground">Status</span>
              <span class="text-xs rounded-full px-2 py-1 {{ $it['status_class'] }}">
                {{ $it['status_label'] }}
              </span>
            </div>
          </div>

          <form method="POST" action="{{ route('company.applications.status', $it['id']) }}" class="grid grid-cols-1 gap-2">
            @csrf
            <select name="status" class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
              <option value="pending"  {{ $it['status']==='pending' ? 'selected' : '' }}>Pending</option>
              <option value="reviewed" {{ $it['status']==='reviewed' ? 'selected' : '' }}>Under Review</option>
              <option value="accepted" {{ $it['status']==='accepted' ? 'selected' : '' }}>Accepted</option>
              <option value="rejected" {{ $it['status']==='rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              Update
            </button>
          </form>
        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No applications found.</div>
      @endforelse
    </div>
  </div>

</div>
@endsection
