<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\Scholarship;
use App\Models\SuccessStory;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Notifications\ContentReviewNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkshopsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $q = trim((string) $request->query('q', ''));

        $query = Workshop::query()
            ->with('company')
            ->withCount([
                'registrations as registered_count' => function ($sub) {
                    if (Schema::hasColumn('workshop_registrations', 'status')) {
                        $sub->where('status', 'registered');
                    }
                }
            ])
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                  ->orWhere('location', 'like', "%{$q}%");
            });
        }

        if ($status !== 'all' && Schema::hasColumn('workshops', 'proposal_status')) {
            $query->where('proposal_status', $status);
        }

        $workshops = $query->paginate(10)->withQueryString();

        $workshops->getCollection()->transform(function ($workshop) {
            $workshop->display_status = $this->resolveWorkshopStatus($workshop);
            $workshop->display_capacity = $this->resolveWorkshopCapacity($workshop);
            $workshop->display_registered = (int) ($workshop->registered_count ?? 0);
            $workshop->display_spots_label = $this->buildSpotsLabel($workshop);
            $workshop->is_company_submission = !is_null($workshop->company_user_id ?? null);
            $workshop->display_owner_name = $workshop->is_company_submission
                ? ($workshop->company?->name ?? 'Company')
                : 'PTC College';

            return $workshop;
        });

        $counts = [
            'all' => Workshop::count(),
            'approved' => Schema::hasColumn('workshops', 'proposal_status')
                ? Workshop::where('proposal_status', 'approved')->count()
                : 0,
            'pending' => Schema::hasColumn('workshops', 'proposal_status')
                ? Workshop::where('proposal_status', 'pending')->count()
                : 0,
            'rejected' => Schema::hasColumn('workshops', 'proposal_status')
                ? Workshop::where('proposal_status', 'rejected')->count()
                : 0,
        ];

        return view('college.workshops', array_merge(
            compact('workshops', 'counts', 'status', 'q'),
            $this->buildNavCounts()
        ));
    }

    public function create()
    {
        return view('college.workshops.create', array_merge([
            'workshop' => null,
            'isEdit' => false,
        ], $this->buildNavCounts()));
    }

    public function store(Request $request)
    {
        $data = $this->validateWorkshop($request);

        $attrs = $this->buildWorkshopAttributes($data, null);

        Workshop::create($attrs);

        return redirect()
            ->route('college.workshops')
            ->with('toast_success', 'Workshop created successfully.');
    }

    public function edit(Workshop $workshop)
    {
        $this->ensureCollegeOwnsWorkshop($workshop);

        return view('college.workshops.create', array_merge([
            'workshop' => $workshop,
            'isEdit' => true,
        ], $this->buildNavCounts()));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $this->ensureCollegeOwnsWorkshop($workshop);

        $data = $this->validateWorkshop($request);

        $attrs = $this->buildWorkshopAttributes($data, $workshop);

        $workshop->update($attrs);

        return redirect()
            ->route('college.workshops')
            ->with('toast_success', 'Workshop updated successfully.');
    }

    public function manage(Workshop $workshop)
    {
        $q = WorkshopRegistration::with('alumni')
            ->where('workshop_id', $workshop->id)
            ->orderByDesc('id');

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $q->where('status', 'registered');
        }

        $registrations = $q->get();

        $workshop->registered_count = $registrations->count();
        $workshop->display_capacity = $this->resolveWorkshopCapacity($workshop);
        $workshop->display_spots_label = $this->buildSpotsLabel($workshop);
        $workshop->display_status = $this->resolveWorkshopStatus($workshop);

        return view('college.workshops-manage', array_merge(
            compact('workshop', 'registrations'),
            $this->buildNavCounts()
        ));
    }

    public function destroy(Workshop $workshop)
    {
        $this->ensureCollegeOwnsWorkshop($workshop);

        $workshop->delete();

        return back()->with('toast_success', 'Workshop deleted successfully.');
    }

    public function approve(Workshop $workshop)
    {
        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshop->forceFill(['proposal_status' => 'approved'])->save();
        }

        $this->notifyWorkshopOwner($workshop, true);
        $this->notifyAlumniAboutApprovedWorkshop($workshop);

        return back()->with('toast_success', 'Workshop approved.');
    }

    public function reject(Request $request, Workshop $workshop)
    {
        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshop->forceFill(['proposal_status' => 'rejected'])->save();
        }

        $reason = trim((string) $request->input('reject_reason', ''));

        $this->notifyWorkshopOwner($workshop, false, $reason);

        return back()->with('toast_success', 'Workshop rejected.');
    }

    private function ensureCollegeOwnsWorkshop(Workshop $workshop): void
    {
        if (!is_null($workshop->company_user_id ?? null)) {
            abort(403);
        }
    }

    private function notifyWorkshopOwner(Workshop $workshop, bool $approved, ?string $reason = null): void
    {
        try {
            $company = $workshop->company;
            if (!$company) {
                return;
            }

            $company->notify(new ContentReviewNotification([
                'kind' => 'content_review',
                'content_type' => 'workshop',
                'content_id' => $workshop->id,
                'status' => $approved ? 'approved' : 'rejected',
                'title' => $approved ? 'Your workshop was approved' : 'Your workshop was rejected',
                'message' => $approved
                    ? 'Your workshop "' . $workshop->title . '" has been approved and is now visible to alumni.'
                    : 'Your workshop "' . $workshop->title . '" was rejected.' . ($reason ? ' Reason: ' . $reason : ''),
                'icon' => 'calendar-days',
                'admin_note' => $reason,
                'url' => route('company.workshops'),
            ]));
        } catch (\Throwable $e) {
        }
    }

    private function notifyAlumniAboutApprovedWorkshop(Workshop $workshop): void
    {
        try {
            $alumniUsers = User::query()->where('role', 'alumni')->get();

            foreach ($alumniUsers as $alumnus) {
                $alumnus->notify(new ContentReviewNotification([
                    'kind' => 'content_review',
                    'content_type' => 'workshop',
                    'content_id' => $workshop->id,
                    'status' => 'approved',
                    'title' => 'New workshop available',
                    'message' => '"' . $workshop->title . '" is now available for registration.',
                    'icon' => 'calendar-days',
                    'url' => route('alumni.workshops'),
                ]));
            }
        } catch (\Throwable $e) {
        }
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

    private function buildWorkshopAttributes(array $data, ?Workshop $existing): array
    {
        $cap = $data['capacity'] ?? null;

        $attrs = [
            'title' => $data['title'],
        ];

        if (Schema::hasColumn('workshops', 'date')) {
            $attrs['date'] = $data['date'];
        }

        if (Schema::hasColumn('workshops', 'time')) {
            $attrs['time'] = $data['time'];
        }

        if (Schema::hasColumn('workshops', 'location')) {
            $attrs['location'] = $data['location'];
        }

        if (Schema::hasColumn('workshops', 'organizer_user_id') && !$existing) {
            $attrs['organizer_user_id'] = Auth::id();
        }

        if (Schema::hasColumn('workshops', 'organizer_role') && !$existing) {
            $attrs['organizer_role'] = 'college';
        }

        if (Schema::hasColumn('workshops', 'company_user_id') && !$existing) {
            $attrs['company_user_id'] = null;
        }

        if (Schema::hasColumn('workshops', 'status')) {
            $attrs['status'] = 'upcoming';
        }

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $attrs['proposal_status'] = 'approved';
        }

        if (Schema::hasColumn('workshops', 'capacity')) {
            $attrs['capacity'] = $cap;
        }

        if (Schema::hasColumn('workshops', 'max_spots')) {
            $attrs['max_spots'] = $cap ? (int) $cap : 0;
        }

        if (Schema::hasColumn('workshops', 'spots')) {
            $registered = 0;

            if ($existing) {
                $registered = WorkshopRegistration::query()
                    ->where('workshop_id', $existing->id)
                    ->when(
                        Schema::hasColumn('workshop_registrations', 'status'),
                        fn ($q) => $q->where('status', 'registered')
                    )
                    ->count();
            }

            $attrs['spots'] = $cap ? max(((int) $cap - $registered), 0) : 0;
        }

        return $attrs;
    }

    private function resolveWorkshopCapacity(Workshop $workshop): ?int
    {
        if (isset($workshop->capacity) && !is_null($workshop->capacity)) {
            return (int) $workshop->capacity;
        }

        if (isset($workshop->max_spots) && (int) $workshop->max_spots > 0) {
            return (int) $workshop->max_spots;
        }

        return null;
    }

    private function buildSpotsLabel(Workshop $workshop): string
    {
        $registered = (int) ($workshop->registered_count ?? 0);
        $capacity = $this->resolveWorkshopCapacity($workshop);

        if (!$capacity) {
            return $registered . ' registered';
        }

        return $registered . '/' . $capacity . ' registered';
    }

    private function resolveWorkshopStatus(Workshop $workshop): string
    {
        if (!empty($workshop->proposal_status) && in_array($workshop->proposal_status, ['pending', 'rejected'], true)) {
            return strtolower((string) $workshop->proposal_status);
        }

        if (!empty($workshop->status)) {
            return strtolower((string) $workshop->status);
        }

        if (!empty($workshop->date)) {
            try {
                $date = Carbon::parse($workshop->date);
                return $date->isPast() ? 'completed' : 'upcoming';
            } catch (\Throwable $e) {
                return 'upcoming';
            }
        }

        return 'upcoming';
    }

    private function buildNavCounts(): array
    {
        return [
            'alumniBadgeCount' => User::where('role', 'alumni')->count(),
            'workshopBadgeCount' => Workshop::count(),
            'jobBadgeCount' => Job::count(),
            'announcementBadgeCount' => Announcement::count(),
            'scholarshipBadgeCount' => Scholarship::count(),
            'successStoryBadgeCount' => SuccessStory::count(),
        ];
    }
}
