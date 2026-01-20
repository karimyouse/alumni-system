@extends('layouts.dashboard')

@php
  $title='Applications';
  $role='Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Browse Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];

  $tabs = [
    ['key'=>'all','label'=>'All'],
    ['key'=>'pending','label'=>'Pending'],
    ['key'=>'reviewed','label'=>'Under Review'],
    ['key'=>'accepted','label'=>'Accepted'],
    ['key'=>'rejected','label'=>'Rejected'],
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-2xl font-bold">Applications</h1>
    <p class="text-sm text-muted-foreground">Review and manage alumni applications</p>
  </div>

  <div class="flex flex-wrap gap-2">
    @foreach($tabs as $t)
      @php
        $active = ($tab ?? 'all') === $t['key'];
        $count = $counts[$t['key']] ?? 0;
      @endphp
      <a href="{{ route('company.applications', ['tab' => $t['key']]) }}"
         class="inline-flex items-center gap-2 rounded-md border border-border px-3 py-2 text-sm
         {{ $active ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/30 hover:text-foreground' }}">
        <span>{{ $t['label'] }}</span>
        <span class="text-xs rounded-full bg-secondary px-2 py-0.5 text-secondary-foreground">{{ $count }}</span>
      </a>
    @endforeach
  </div>

  <div class="rounded-xl border border-border bg-card overflow-hidden">
    <div class="overflow-auto">
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
                    <option value="pending"  {{ $it['status']==='pending'?'selected':'' }}>Pending</option>
                    <option value="reviewed" {{ $it['status']==='reviewed'?'selected':'' }}>Under Review</option>
                    <option value="accepted" {{ $it['status']==='accepted'?'selected':'' }}>Accepted</option>
                    <option value="rejected" {{ $it['status']==='rejected'?'selected':'' }}>Rejected</option>
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
  </div>

</div>
@endsection
