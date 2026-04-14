@extends('layouts.dashboard')

@php
  $title = __('Jobs Review');
  $role='College';

  $nav = [
    ['label'=>'Overview','href'=>'/college','icon'=>'layout-dashboard'],
    ['label'=>'Manage Alumni','href'=>'/college/alumni','icon'=>'users','badge'=>$alumniBadgeCount ?? 0],
    ['label'=>'Workshops','href'=>'/college/workshops','icon'=>'calendar-days','badge'=>$workshopBadgeCount ?? 0],
    ['label'=>'Job Postings','href'=>'/college/jobs','icon'=>'briefcase','badge'=>$jobBadgeCount ?? 0],
    ['label'=>'Announcements','href'=>'/college/announcements','icon'=>'megaphone','badge'=>$announcementBadgeCount ?? 0],
    ['label'=>'Scholarships','href'=>'/college/scholarships','icon'=>'graduation-cap','badge'=>$scholarshipBadgeCount ?? 0],
    ['label'=>'Success Stories','href'=>'/college/success-stories','icon'=>'award','badge'=>$successStoryBadgeCount ?? 0],
    ['label'=>'Reports','href'=>'/college/reports','icon'=>'bar-chart-3'],
  ];

  $pill = fn($s) => match($s){
    'approved' => ['Approved','bg-green-500/15 text-green-400'],
    'pending' => ['Pending','bg-orange-500/15 text-orange-400'],
    'rejected' => ['Rejected','bg-red-500/15 text-red-400'],
    default => [ucfirst($s ?? '—'),'bg-secondary text-secondary-foreground'],
  };
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold">Jobs Review</h1>
      <p class="text-sm text-muted-foreground">Approve company jobs and manage college jobs</p>
    </div>

    <a href="{{ route('college.jobs.create') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90 transition sm:w-auto">
      <i data-lucide="plus" class="h-4 w-4"></i>
      Post Job
    </a>
  </div>

  <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
    @php
      $tabs = [
        ['key'=>'all','label'=>'All','count'=>$counts['all'] ?? 0],
        ['key'=>'approved','label'=>'Approved','count'=>$counts['approved'] ?? 0],
        ['key'=>'pending','label'=>'Pending','count'=>$counts['pending'] ?? 0],
        ['key'=>'rejected','label'=>'Rejected','count'=>$counts['rejected'] ?? 0],
      ];
    @endphp

    @foreach($tabs as $t)
      <a href="{{ route('college.jobs', ['status'=>$t['key'], 'q'=>$q]) }}"
         class="inline-flex items-center justify-center gap-2 rounded-md border border-border px-3 py-2 text-sm
         {{ ($status ?? 'all')===$t['key'] ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/30 hover:text-foreground' }}">
        {{ $t['label'] }}
        <span class="text-xs rounded-full bg-secondary px-2 py-0.5 text-secondary-foreground">{{ $t['count'] }}</span>
      </a>
    @endforeach
  </div>

  <form method="GET" action="{{ route('college.jobs') }}" class="rounded-xl border border-border bg-card p-5">
    <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
    <div class="flex flex-col md:flex-row gap-3 md:items-center">
      <div class="flex-1">
        <input name="q" value="{{ $q }}"
               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
               placeholder="Search by title, company, location">
      </div>
      <div class="grid grid-cols-2 gap-2 md:flex">
        <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground hover:opacity-90">
          Search
        </button>
        <a href="{{ route('college.jobs', ['status'=>$status ?? 'all']) }}"
           class="inline-flex items-center justify-center rounded-md border border-border px-4 py-2 text-sm hover:bg-accent/50">
          Reset
        </a>
      </div>
    </div>
  </form>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="hidden overflow-auto md:block">
      <table class="w-full">
        <thead class="border-b bg-muted/40">
          <tr>
            <th class="text-left p-4 font-medium">Job</th>
            <th class="text-left p-4 font-medium">Owner</th>
            <th class="text-left p-4 font-medium">Location</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Featured</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jobs as $j)
            @php
              [$stLabel,$stClass] = $pill($j->approval_status ?? 'approved');

              $isCompanyJob = (($j->organizer_role ?? null) === 'company')
                || (!is_null($j->company_user_id ?? null) && ($j->organizer_role ?? null) !== 'college');
            @endphp

            <tr class="border-b last:border-0">
              <td class="p-4">
                <div class="font-semibold">{{ $j->title }}</div>
                <div class="text-xs text-muted-foreground">{{ $j->type ?? '—' }} • {{ $j->salary ?? '—' }}</div>
              </td>

              <td class="p-4 text-sm text-muted-foreground">
                {{ $j->display_owner_name ?? ($isCompanyJob ? ($j->company_name ?? 'Company') : 'PTC College') }}
              </td>

              <td class="p-4 text-sm text-muted-foreground">{{ $j->location ?? '—' }}</td>

              <td class="p-4">
                <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ $stLabel }}</span>

                <div class="text-[11px] text-muted-foreground mt-1">
                  {{ $isCompanyJob ? 'Company submission' : 'College job' }}
                </div>

                <div class="text-[11px] text-muted-foreground mt-1">
                  Accepted applicants: {{ $j->display_accepted_count ?? 0 }}
                </div>

                @if(($j->approval_status ?? '') === 'rejected' && $j->reject_reason)
                  <div class="text-[11px] text-muted-foreground mt-1">
                    Reason: {{ $j->reject_reason }}
                  </div>
                @endif
              </td>

              <td class="p-4">
                <form method="POST" action="{{ route('college.jobs.feature', $j) }}">
                  @csrf
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    {{ $j->is_featured ? 'Yes' : 'No' }}
                  </button>
                </form>
              </td>

              <td class="p-4">
                <div class="flex flex-col gap-2">
                  <a href="{{ route('college.jobs.applicants', $j) }}"
                     class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50 text-center">
                    View Applicants
                    @if(($j->display_applicants_count ?? 0) > 0)
                      ({{ $j->display_applicants_count }})
                    @endif
                  </a>

                  @if($isCompanyJob)
                    @if(($j->approval_status ?? 'approved') === 'pending')
                      <form method="POST" action="{{ route('college.jobs.approve', $j) }}">
                        @csrf
                        <button class="w-full rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">
                          Approve
                        </button>
                      </form>

                      <form method="POST" action="{{ route('college.jobs.reject', $j) }}" class="space-y-2">
                        @csrf
                        <input name="reject_reason"
                               class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                               placeholder="Reject reason (optional)">
                        <button class="w-full rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                          Reject
                        </button>
                      </form>
                    @endif
                  @else
                    <a href="{{ route('college.jobs.edit', $j) }}"
                       class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50 text-center">
                      Edit
                    </a>

                    <form method="POST" action="{{ route('college.jobs.delete', $j) }}"
                          onsubmit="return confirm('Delete this job?');">
                      @csrf
                      <button class="w-full rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                        Delete
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-sm text-muted-foreground" colspan="6">No jobs found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="divide-y divide-border md:hidden">
      @forelse($jobs as $j)
        @php
          [$stLabel,$stClass] = $pill($j->approval_status ?? 'approved');

          $isCompanyJob = (($j->organizer_role ?? null) === 'company')
            || (!is_null($j->company_user_id ?? null) && ($j->organizer_role ?? null) !== 'college');
        @endphp

        <div class="p-4 space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
              <i data-lucide="briefcase" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="font-semibold leading-snug break-words">{{ $j->title }}</div>
              <div class="text-xs text-muted-foreground mt-1 break-words">
                {{ $j->type ?? '—' }} • {{ $j->salary ?? '—' }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-2 text-sm">
            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Owner</span>
              <span class="text-right break-words">{{ $j->display_owner_name ?? ($isCompanyJob ? ($j->company_name ?? 'Company') : 'PTC College') }}</span>
            </div>

            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Location</span>
              <span class="text-right break-words">{{ $j->location ?? '—' }}</span>
            </div>

            <div class="flex justify-between gap-3">
              <span class="text-muted-foreground">Featured</span>
              <form method="POST" action="{{ route('college.jobs.feature', $j) }}">
                @csrf
                <button class="rounded-md border border-border px-3 py-1.5 text-sm hover:bg-accent/50">
                  {{ $j->is_featured ? 'Yes' : 'No' }}
                </button>
              </form>
            </div>

            <div class="flex items-start justify-between gap-3">
              <span class="text-muted-foreground">Status</span>
              <div class="text-right">
                <span class="text-xs rounded-full px-2 py-1 {{ $stClass }}">{{ $stLabel }}</span>
                <div class="text-[11px] text-muted-foreground mt-1">
                  {{ $isCompanyJob ? 'Company submission' : 'College job' }}
                </div>
                <div class="text-[11px] text-muted-foreground mt-1">
                  Accepted applicants: {{ $j->display_accepted_count ?? 0 }}
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-2">
            <a href="{{ route('college.jobs.applicants', $j) }}"
               class="inline-flex items-center justify-center rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
              View Applicants
              @if(($j->display_applicants_count ?? 0) > 0)
                ({{ $j->display_applicants_count }})
              @endif
            </a>

            @if($isCompanyJob)
              @if(($j->approval_status ?? 'approved') === 'pending')
                <form method="POST" action="{{ route('college.jobs.approve', $j) }}">
                  @csrf
                  <button class="w-full rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90">
                    Approve
                  </button>
                </form>

                <form method="POST" action="{{ route('college.jobs.reject', $j) }}" class="space-y-2">
                  @csrf
                  <input name="reject_reason"
                         class="w-full rounded-md border border-input bg-background/60 px-3 py-2 text-sm"
                         placeholder="Reject reason (optional)">
                  <button class="w-full rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    Reject
                  </button>
                </form>
              @endif
            @else
              <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('college.jobs.edit', $j) }}"
                   class="inline-flex items-center justify-center rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                  Edit
                </a>

                <form method="POST" action="{{ route('college.jobs.delete', $j) }}"
                      onsubmit="return confirm('Delete this job?');">
                  @csrf
                  <button class="w-full rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50">
                    Delete
                  </button>
                </form>
              </div>
            @endif
          </div>
        </div>
      @empty
        <div class="p-6 text-sm text-muted-foreground">No jobs found.</div>
      @endforelse
    </div>
  </div>

  <div>
    {{ $jobs->links() }}
  </div>

</div>
@endsection
