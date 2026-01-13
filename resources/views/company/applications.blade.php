@extends('layouts.dashboard')

@php
  $title = 'Applications';
  $role  = 'Company';

  $nav = [
    ['label'=>'Overview','href'=>'/company','icon'=>'layout-dashboard'],
    ['label'=>'Jobs','href'=>'/company/jobs','icon'=>'briefcase'],
    ['label'=>'Alumni','href'=>'/company/alumni','icon'=>'users'],
    ['label'=>'Applications','href'=>'/company/applications','icon'=>'file-text'],
    ['label'=>'Workshops','href'=>'/company/workshops','icon'=>'calendar-days'],
  ];

  $apps = [
    ['id'=>'1','applicant'=>'Ahmed Al-Hassan','avatar'=>'AH','job'=>'Frontend Developer','applied'=>'Dec 22, 2025','status'=>'pending'],
    ['id'=>'2','applicant'=>'Sara Ali','avatar'=>'SA','job'=>'Frontend Developer','applied'=>'Dec 21, 2025','status'=>'reviewed'],
    ['id'=>'3','applicant'=>'Omar Khalil','avatar'=>'OK','job'=>'Backend Engineer','applied'=>'Dec 20, 2025','status'=>'accepted'],
    ['id'=>'4','applicant'=>'Layla Hassan','avatar'=>'LH','job'=>'UI/UX Designer','applied'=>'Dec 19, 2025','status'=>'rejected'],
    ['id'=>'5','applicant'=>'Mohammed Nasser','avatar'=>'MN','job'=>'Backend Engineer','applied'=>'Dec 18, 2025','status'=>'pending'],
  ];

  $filter = fn($s) => array_values(array_filter($apps, fn($a)=> $s==='all' ? true : $a['status']===$s));
  $counts = [
    'all' => count($apps),
    'pending' => count($filter('pending')),
    'reviewed' => count($filter('reviewed')),
    'accepted' => count($filter('accepted')),
  ];

  $statusBadge = function($s){
    return match($s){
      'pending'  => ['Pending','bg-secondary text-secondary-foreground','clock'],
      'reviewed' => ['Under Review','border border-blue-500 text-blue-400',''],
      'accepted' => ['Accepted','bg-green-600/20 text-green-400','check-circle'],
      'rejected' => ['Rejected','bg-red-500/15 text-red-400','x-circle'],
      default    => [$s,'bg-secondary text-secondary-foreground',''],
    };
  };
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Applications</h1>
    <p class="text-muted-foreground">Review and manage incoming applications</p>
  </div>

  {{-- Tabs (مثل React) --}}
  <div class="inline-flex rounded-lg bg-muted p-1 gap-1" id="tabs">
    @foreach(['all'=>'All','pending'=>'Pending','reviewed'=>'Reviewed','accepted'=>'Accepted'] as $key=>$label)
      <button type="button"
              class="px-3 py-2 text-sm rounded-md {{ $key==='all' ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground' }}"
              data-tab="{{ $key }}"
              data-testid="tab-{{ $key }}">
        {{ $label }} ({{ $counts[$key] }})
      </button>
    @endforeach
  </div>

  @foreach(['all','pending','reviewed','accepted'] as $tab)
    @php $list = $filter($tab); @endphp
    <div class="space-y-3 tab-panel {{ $tab!=='all' ? 'hidden' : '' }}" data-panel="{{ $tab }}">
      @foreach($list as $app)
        @php [$txt,$cls,$ic] = $statusBadge($app['status']); @endphp
        <div class="rounded-xl border border-border bg-card" data-testid="card-application-{{ $app['id'] }}">
          <div class="p-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
              <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                {{ $app['avatar'] }}
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <h4 class="font-medium">{{ $app['applicant'] }}</h4>
                  <span class="inline-flex items-center rounded-full px-2 py-1 text-xs {{ $cls }}">
                    @if($ic)<i data-lucide="{{ $ic }}" class="h-3 w-3 mr-1"></i>@endif
                    {{ $txt }}
                  </span>
                </div>
                <p class="text-sm text-muted-foreground">
                  Applied for {{ $app['job'] }} on {{ $app['applied'] }}
                </p>
              </div>

              <div class="flex gap-2">
                <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50"
                        data-testid="button-view-{{ $app['id'] }}">
                  <i data-lucide="eye" class="h-4 w-4 mr-1 inline"></i>
                  View
                </button>

                @if($app['status']==='pending')
                  <button class="rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50"
                          data-testid="button-reject-{{ $app['id'] }}">Reject</button>
                  <button class="rounded-md bg-primary px-3 py-2 text-sm text-primary-foreground hover:opacity-90"
                          data-testid="button-accept-{{ $app['id'] }}">Accept</button>
                @endif
              </div>

            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endforeach
</div>

<script>
  const tabs = document.getElementById('tabs');
  const btns = tabs.querySelectorAll('[data-tab]');
  const panels = document.querySelectorAll('.tab-panel');

  function setActive(tab) {
    btns.forEach(b => {
      const isActive = b.dataset.tab === tab;
      b.classList.toggle('bg-background', isActive);
      b.classList.toggle('shadow-sm', isActive);
      b.classList.toggle('text-foreground', isActive);
      b.classList.toggle('text-muted-foreground', !isActive);
    });
    panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== tab));
  }

  btns.forEach(b => b.addEventListener('click', () => setActive(b.dataset.tab)));
</script>
@endsection
