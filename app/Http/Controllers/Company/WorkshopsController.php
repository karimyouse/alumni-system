<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Notifications\ContentReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkshopsController extends Controller
{
    private function computeWorkshopState($w): string
    {
        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $proposal = strtolower((string) ($w->proposal_status ?? ''));
            if ($proposal === 'pending') return 'pending';
            if ($proposal === 'rejected') return 'rejected';
        }

        if (Schema::hasColumn('workshops', 'status') && !empty($w->status)) {
            return strtolower($w->status);
        }

        return 'upcoming';
    }

    private function registrationsCount(int $workshopId): int
    {
        $q = WorkshopRegistration::where('workshop_id', $workshopId);

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $q->where('status', 'registered');
        }

        return $q->count();
    }

    public function index()
    {
        $companyId = Auth::id();

        $workshops = Workshop::query()
            ->where('company_user_id', $companyId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($w) {
                $state = $this->computeWorkshopState($w);

                $pill = match ($state) {
                    'completed' => ['completed', 'bg-secondary text-secondary-foreground'],
                    'pending'   => ['pending', 'bg-orange-500/15 text-orange-400'],
                    'rejected'  => ['rejected', 'bg-red-500/15 text-red-400'],
                    default     => ['upcoming', 'bg-blue-500/15 text-blue-400'],
                };

                return [
                    'id' => $w->id,
                    'title' => $w->title ?? 'Workshop',
                    'date' => $w->date ?? ($w->workshop_date ?? ''),
                    'time' => $w->time ?? ($w->time_range ?? ''),
                    'location' => $w->location ?? ($w->venue ?? ''),
                    'status' => $pill[0],
                    'status_class' => $pill[1],
                    'registrations' => $this->registrationsCount($w->id),
                ];
            });

        return view('company.workshops.index', array_merge(
            compact('workshops'),
            $this->buildNavCounts()
        ));
    }

    public function create()
    {
        return view('company.workshops.create', array_merge([
            'workshop' => null,
            'isEdit' => false,
        ], $this->buildNavCounts()));
    }

    public function store(Request $request)
    {
        $data = $this->validateWorkshop($request);

        $cap = $data['capacity'] ?? null;

        $attrs = [
            'title' => $data['title'],
            'company_user_id' => Auth::id(),
        ];

        if (Schema::hasColumn('workshops', 'organizer_user_id')) $attrs['organizer_user_id'] = Auth::id();
        if (Schema::hasColumn('workshops', 'organizer_role')) $attrs['organizer_role'] = 'company';

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $attrs['proposal_status'] = 'pending';
        }

        if (Schema::hasColumn('workshops', 'date')) $attrs['date'] = $data['date'];
        if (Schema::hasColumn('workshops', 'time')) $attrs['time'] = $data['time'];
        if (Schema::hasColumn('workshops', 'location')) $attrs['location'] = $data['location'];

        if (Schema::hasColumn('workshops', 'status')) $attrs['status'] = 'upcoming';
        if (Schema::hasColumn('workshops', 'capacity')) $attrs['capacity'] = $cap;
        if (Schema::hasColumn('workshops', 'max_spots')) $attrs['max_spots'] = $cap ? (int) $cap : 0;
        if (Schema::hasColumn('workshops', 'spots')) $attrs['spots'] = $cap ? (int) $cap : 0;

        $workshop = Workshop::create($attrs);

        $this->notifyCollegesAboutWorkshop($workshop);

        return redirect()->route('company.workshops')
            ->with('toast_success', 'Workshop submitted for college review.');
    }

    public function edit(Workshop $workshop)
    {
        $this->ensureOwner($workshop);

        return view('company.workshops.create', array_merge([
            'workshop' => $workshop,
            'isEdit' => true,
        ], $this->buildNavCounts()));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $this->ensureOwner($workshop);

        $data = $this->validateWorkshop($request);

        $cap = $data['capacity'] ?? null;

        $attrs = [
            'title' => $data['title'],
        ];

        if (Schema::hasColumn('workshops', 'date')) $attrs['date'] = $data['date'];
        if (Schema::hasColumn('workshops', 'time')) $attrs['time'] = $data['time'];
        if (Schema::hasColumn('workshops', 'location')) $attrs['location'] = $data['location'];

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $attrs['proposal_status'] = 'pending';
        }

        if (Schema::hasColumn('workshops', 'capacity')) $attrs['capacity'] = $cap;
        if (Schema::hasColumn('workshops', 'max_spots')) $attrs['max_spots'] = $cap ? (int) $cap : 0;

        if (Schema::hasColumn('workshops', 'spots')) {
            $registered = WorkshopRegistration::query()
                ->where('workshop_id', $workshop->id)
                ->when(
                    Schema::hasColumn('workshop_registrations', 'status'),
                    fn ($q) => $q->where('status', 'registered')
                )
                ->count();

            $attrs['spots'] = $cap ? max(((int) $cap - $registered), 0) : 0;
        }

        $workshop->update($attrs);

        $this->notifyCollegesAboutWorkshop($workshop, true);

        return redirect()->route('company.workshops')
            ->with('toast_success', 'Workshop updated and re-submitted for college review.');
    }

    public function destroy(Workshop $workshop)
    {
        $this->ensureOwner($workshop);

        $workshop->delete();

        return back()->with('toast_success', 'Workshop deleted successfully.');
    }

    public function manage(Workshop $workshop)
    {
        $this->ensureOwner($workshop);

        $regs = WorkshopRegistration::with('alumni')
            ->where('workshop_id', $workshop->id)
            ->orderByDesc('id');

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $regs->where('status', 'registered');
        }

        $registrations = $regs->get();

        return view('company.workshops.manage', array_merge(
            compact('workshop', 'registrations'),
            $this->buildNavCounts()
        ));
    }

    private function validateWorkshop(Request $request): array
    {
        return $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'date'     => ['required', 'string', 'max:255'],
            'time'     => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function ensureOwner(Workshop $workshop): void
    {
        if ((int) ($workshop->company_user_id ?? 0) !== (int) Auth::id()) {
            abort(403);
        }
    }

    private function notifyCollegesAboutWorkshop(Workshop $workshop, bool $updated = false): void
    {
        try {
            $colleges = User::query()->where('role', 'college')->get();
            $companyName = Auth::user()?->name ?? 'Company';

            foreach ($colleges as $college) {
                $college->notify(new ContentReviewNotification([
                    'kind' => 'content_review',
                    'content_type' => 'workshop',
                    'content_id' => $workshop->id,
                    'status' => 'pending',
                    'title' => $updated ? 'Company updated a workshop for review' : 'New company workshop needs review',
                    'message' => $companyName . ' submitted "' . $workshop->title . '" for review.',
                    'icon' => 'calendar-days',
                    'url' => route('college.workshops'),
                ]));
            }
        } catch (\Throwable $e) {
        }
    }

    private function buildNavCounts(): array
{
    $companyId = Auth::id();

    $jobsQuery = Job::query()
        ->where('company_user_id', $companyId);

    if (Schema::hasColumn('jobs', 'organizer_role')) {
        $jobsQuery->where('organizer_role', 'company');
    }

    $jobIds = (clone $jobsQuery)->pluck('id');

    $applicationsQuery = JobApplication::query()->whereIn('job_id', $jobIds);

    $workshopsQuery = Workshop::query()->where('company_user_id', $companyId);

    if (Schema::hasColumn('workshops', 'organizer_role')) {
        $workshopsQuery->where('organizer_role', 'company');
    }

    return [
        'jobBadgeCount' => (clone $jobsQuery)->count(),
        'alumniBadgeCount' => User::where('role', 'alumni')->count(),
        'applicationBadgeCount' => (clone $applicationsQuery)->count(),
        'workshopBadgeCount' => (clone $workshopsQuery)->count(),
    ];
}
}
