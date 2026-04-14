<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\ScholarshipApplication;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkshopsController extends Controller
{
    public function index()
    {
        $userId = (int) Auth::id();

        $workshopsQuery = Workshop::query()
            ->with(['company.companyProfile'])
            ->when(
                Schema::hasColumn('workshops', 'proposal_status'),
                fn ($q) => $q->where('proposal_status', 'approved')
            )
            ->when(
                Schema::hasColumn('workshops', 'status'),
                fn ($q) => $q->where('status', 'upcoming')
            )
            ->withCount([
                'registrations as registered_count' => function ($q) {
                    if (Schema::hasColumn('workshop_registrations', 'status')) {
                        $q->where('status', 'registered');
                    }
                }
            ])
            ->orderByDesc('id');

        $workshops = $workshopsQuery->get();

        $regQuery = WorkshopRegistration::where('alumni_user_id', $userId);

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $regQuery->where('status', 'registered');
        }

        $registeredIds = $regQuery->pluck('workshop_id')->toArray();

        $navBadges = $this->getNavBadges($userId);

        return view('alumni.workshops', [
            'workshops' => $workshops,
            'registeredIds' => $registeredIds,
            'jobBadgeCount' => $navBadges['jobBadgeCount'],
            'recommendationsReceived' => $navBadges['recommendationsReceived'],
            'applicationsBadgeCount' => $navBadges['applicationsBadgeCount'],
        ]);
    }

    public function register(Workshop $workshop)
    {
        $userId = (int) Auth::id();

        if (!$this->isWorkshopAvailableToAlumni($workshop)) {
            return back()->with('toast_success', 'This workshop is not available yet.');
        }

        $registrationQuery = WorkshopRegistration::where('workshop_id', $workshop->id)
            ->where('alumni_user_id', $userId);

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $already = (clone $registrationQuery)
                ->where('status', 'registered')
                ->exists();

            if ($already) {
                return back()->with('toast_success', 'You are already registered.');
            }
        } else {
            $already = (clone $registrationQuery)->exists();

            if ($already) {
                return back()->with('toast_success', 'You are already registered.');
            }
        }

        if (Schema::hasColumn('workshops', 'capacity')) {
            $cap = $workshop->capacity;

            if (!is_null($cap)) {
                $countQuery = WorkshopRegistration::where('workshop_id', $workshop->id);

                if (Schema::hasColumn('workshop_registrations', 'status')) {
                    $countQuery->where('status', 'registered');
                }

                $count = $countQuery->count();

                if ($count >= (int) $cap) {
                    return back()->with('toast_success', 'Workshop is full.');
                }
            }
        }

        $attrs = [
            'workshop_id' => $workshop->id,
            'alumni_user_id' => $userId,
        ];

        $values = [];

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $values['status'] = 'registered';
        }

        WorkshopRegistration::updateOrCreate($attrs, $values);

        return back()->with('toast_success', 'Successfully registered!');
    }

    public function cancel(Workshop $workshop)
    {
        $userId = (int) Auth::id();

        $regQuery = WorkshopRegistration::where('workshop_id', $workshop->id)
            ->where('alumni_user_id', $userId);

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $regQuery->where('status', 'registered');
        }

        $reg = $regQuery->first();

        if (!$reg) {
            return back()->with('toast_success', 'No active registration found.');
        }

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $reg->update(['status' => 'cancelled']);
        } else {
            $reg->delete();
        }

        return back()->with('toast_success', 'Registration cancelled.');
    }

    private function isWorkshopAvailableToAlumni(Workshop $workshop): bool
    {
        if (Schema::hasColumn('workshops', 'proposal_status')) {
            if (($workshop->proposal_status ?? 'approved') !== 'approved') {
                return false;
            }
        }

        if (Schema::hasColumn('workshops', 'status')) {
            if (($workshop->status ?? 'upcoming') !== 'upcoming') {
                return false;
            }
        }

        return true;
    }

    private function getNavBadges(int $userId): array
    {
        $jobsQuery = Job::query();

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }

        if (Schema::hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }

        $registeredWorkshopsQuery = WorkshopRegistration::where('alumni_user_id', $userId);
        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $registeredWorkshopsQuery->where('status', 'registered');
        }

        $jobApplicationsCount = JobApplication::where('alumni_user_id', $userId)->count();
        $scholarshipApplicationsCount = class_exists(\App\Models\ScholarshipApplication::class)
            ? ScholarshipApplication::where('alumni_user_id', $userId)->count()
            : 0;
        $registeredWorkshopsCount = (clone $registeredWorkshopsQuery)->count();

        return [
            'jobBadgeCount' => (clone $jobsQuery)->count(),
            'recommendationsReceived' => Recommendation::where('to_user_id', $userId)->count(),
            'applicationsBadgeCount' => $jobApplicationsCount + $scholarshipApplicationsCount + $registeredWorkshopsCount,
        ];
    }
}
