@extends('layouts.dashboard')

@php
  $title = __('Job Applicants');
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'My Job Postings','href'=>'/company/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text','badge'=>$applicationBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
  ];

  $statusPill = fn($s) => match($s){
    'pending' => ['Pending','bg-muted text-foreground'],
    'reviewed' => ['Under Review','bg-blue-500/15 text-blue-400'],
    'accepted' => ['Accepted','bg-green-500/15 text-green-400'],
    'rejected' => ['Rejected','bg-red-500/15 text-red-400'],
    default => [ucfirst((string)$s),'bg-secondary text-secondary-foreground'],
  };
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold leading-tight break-words">{{ $job->title }}</h1>
      <p class="text-sm text-muted-foreground break-words">
        {{ $job->company_name }} • {{ $job->location ?: '—' }} • {{ $job->type ?: '—' }}
      </p>
    </div>

    <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
      <a href="{{ route('company.jobs.edit', $job) }}"
         class="inline-flex items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Edit Job
      </a>

      <a href="{{ route('company.jobs') }}"
         class="inline-flex items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Back
      </a>
    </div>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="hidden overflow-auto md:block">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Applicant</th>
            <th class="text-left p-4 font-medium">Applied</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Update</th>
          </tr>
        </thead>
        <tbody>
          @forelse($apps as $a)
            @php [$label,$cls] = $statusPill($a->status ?? 'pending'); @endphp
            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $a->alumni?->name ?? 'Alumni' }}</div>
                <div class="text-xs text-muted-foreground">
                  {{ $a->alumni?->academic_id ?? '' }} • {{ $a->alumni?->email ?? '' }}
                </div>
              </td>

              <td class="p-4 text-sm text-muted-foreground">
                {{ $a->applied_date ?? $a->created_at?->format('M d, Y') }}
              </td>

              <td class="p-4">
                <span class="text-xs rounded-full px-2 py-1 {{ $cls }}">{{ $label }}</span>
              </td>

              <td class="p-4">
                <form method="POST" action="{{ route('company.applications.status', $a) }}" class="flex items-center gap-2">
                  @csrf
                  <select name="status" class="rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
                    <option value="pending"  {{ ($a->status==='pending')?'selected':'' }}>Pending</option>
                    <option value="reviewed" {{ ($a->status==='reviewed')?'selected':'' }}>Under Review</option>
                    <option value="accepted" {{ ($a->status==='accepted')?'selected':'' }}>Accepted</option>
                    <option value="rejected" {{ ($a->status==='rejected')?'selected':'' }}>Rejected</option>
                  </select>

                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    Update
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="4">No applicants yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="divide-y divide-border md:hidden">
      @forelse($apps as $a)
        @php [$label,$cls] = $statusPill($a->status ?? 'pending'); @endphp

        <div class="p-4 space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
              <i data-lucide="user" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="font-semibold leading-snug break-words">{{ $a->alumni?->name ?? 'Alumni' }}</div>
              <div class="text-xs text-muted-foreground mt-1 break-words">
                {{ $a->alumni?->academic_id ?? '' }} • {{ $a->alumni?->email ?? '' }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-2 text-sm">
            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Applied</span>
              <span class="text-right break-words">{{ $a->applied_date ?? $a->created_at?->format('M d, Y') }}</span>
            </div>

            <div class="flex items-center justify-between gap-3">
              <span class="text-muted-foreground">Status</span>
              <span class="text-xs rounded-full px-2 py-1 {{ $cls }}">{{ $label }}</span>
            </div>
          </div>

          <form method="POST" action="{{ route('company.applications.status', $a) }}" class="grid grid-cols-1 gap-2">
            @csrf
            <select name="status" class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm">
              <option value="pending"  {{ ($a->status==='pending')?'selected':'' }}>Pending</option>
              <option value="reviewed" {{ ($a->status==='reviewed')?'selected':'' }}>Under Review</option>
              <option value="accepted" {{ ($a->status==='accepted')?'selected':'' }}>Accepted</option>
              <option value="rejected" {{ ($a->status==='rejected')?'selected':'' }}>Rejected</option>
            </select>

            <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              Update
            </button>
          </form>
        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No applicants yet.</div>
      @endforelse
    </div>
  </div>

</div>
@endsection
