@extends('layouts.dashboard')

@php
  $title='Job Applicants';
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Browse Alumni','href'=>'/college/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone','badge'=>$announcementBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap','badge'=>$scholarshipBadgeCount ?? 0],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award','badge'=>$successStoryBadgeCount ?? 0],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];

  $statusPill = fn($s) => match(strtolower((string)$s)){
    'pending' => ['Pending','bg-muted text-foreground'],
    'reviewed' => ['Under Review','bg-blue-500/15 text-blue-400'],
    'accepted' => ['Accepted','bg-green-500/15 text-green-400'],
    'rejected' => ['Rejected','bg-red-500/15 text-red-400'],
    default => [ucfirst((string)$s),'bg-secondary text-secondary-foreground'],
  };
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-2xl font-bold">{{ $job->title }}</h1>
      <p class="text-sm text-muted-foreground">
        {{ $job->company_name ?: 'PTC College' }} • {{ $job->location ?: '—' }} • {{ $job->type ?: '—' }}
      </p>
    </div>

    <div class="flex items-center gap-2">
      @if(($job->organizer_role ?? 'college') !== 'company')
        <a href="{{ route('college.jobs.edit', $job) }}"
           class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          Edit Job
        </a>
      @endif

      <a href="{{ route('college.jobs') }}"
         class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
        Back
      </a>
    </div>
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
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
                <form method="POST" action="{{ route('college.jobs.applicants.updateStatus', [$job, $a]) }}" class="flex items-center gap-2">
                  @csrf

                  <select name="status"
                          class="rounded-md border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                    <option value="pending" {{ ($a->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ ($a->status ?? '') === 'reviewed' ? 'selected' : '' }}>Under Review</option>
                    <option value="accepted" {{ ($a->status ?? '') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ ($a->status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                  </select>

                  <button type="submit"
                          class="rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
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
  </div>
</div>
@endsection
