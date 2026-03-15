<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ScholarshipsController extends Controller
{
    public function index()
    {
        $userId = (int) Auth::id();

        $scholarshipsQuery = Scholarship::query()->orderByDesc('id');

        if (Schema::hasColumn('scholarships', 'status')) {
            $scholarshipsQuery->where('status', 'active');
        }

        $scholarships = $scholarshipsQuery
            ->paginate(10)
            ->through(function ($scholarship) {
                $scholarship->is_open_now = $this->isScholarshipOpen($scholarship);
                return $scholarship;
            });

        $appliedIds = ScholarshipApplication::where('alumni_user_id', $userId)
            ->pluck('scholarship_id')
            ->toArray();

        $navBadges = $this->getNavBadges($userId);

        return view('alumni.scholarships', [
            'scholarships' => $scholarships,
            'appliedIds' => $appliedIds,
            'jobBadgeCount' => $navBadges['jobBadgeCount'],
            'workshopBadgeCount' => $navBadges['workshopBadgeCount'],
            'recommendationsReceived' => $navBadges['recommendationsReceived'],
            'applicationsBadgeCount' => $navBadges['applicationsBadgeCount'],
        ]);
    }

    public function show(Scholarship $scholarship)
    {
        $userId = (int) Auth::id();

        if (!$this->isScholarshipVisibleToAlumni($scholarship)) {
            abort(404);
        }

        $alreadyApplied = ScholarshipApplication::where('alumni_user_id', $userId)
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        $navBadges = $this->getNavBadges($userId);

        return view('alumni.scholarship-show', [
            'scholarship' => $scholarship,
            'alreadyApplied' => $alreadyApplied,
            'jobBadgeCount' => $navBadges['jobBadgeCount'],
            'workshopBadgeCount' => $navBadges['workshopBadgeCount'],
            'recommendationsReceived' => $navBadges['recommendationsReceived'],
            'applicationsBadgeCount' => $navBadges['applicationsBadgeCount'],
        ]);
    }

    public function apply(Scholarship $scholarship)
    {
        $userId = (int) Auth::id();

        if (!$this->isScholarshipVisibleToAlumni($scholarship)) {
            return back()->with('toast_success', 'This scholarship is not available.');
        }

        if (!$this->isScholarshipOpen($scholarship)) {
            return back()->with('toast_success', 'The application deadline for this scholarship has passed.');
        }

        $exists = ScholarshipApplication::where('alumni_user_id', $userId)
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        if ($exists) {
            return back()->with('toast_success', 'You already applied for this scholarship.');
        }

        ScholarshipApplication::create([
            'scholarship_id' => $scholarship->id,
            'alumni_user_id' => $userId,
            'status' => 'pending',
            'applied_date' => now()->format('M d, Y'),
        ]);

        return redirect()
            ->route('alumni.scholarships.show', $scholarship)
            ->with('toast_success', 'Scholarship application submitted!');
    }

    private function isScholarshipVisibleToAlumni(Scholarship $scholarship): bool
    {
        if (Schema::hasColumn('scholarships', 'status')) {
            if (($scholarship->status ?? 'active') !== 'active') {
                return false;
            }
        }

        return true;
    }

    private function isScholarshipOpen(Scholarship $scholarship): bool
    {
        $deadline = trim((string) ($scholarship->deadline ?? ''));

        if ($deadline === '') {
            return true;
        }

        try {
            return Carbon::parse($deadline)->endOfDay()->isFuture();
        } catch (\Throwable $e) {
            return true;
        }
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

        $workshopsQuery = Workshop::query();

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshopsQuery->where('proposal_status', 'approved');
        }

        if (Schema::hasColumn('workshops', 'status')) {
            $workshopsQuery->where('status', 'upcoming');
        }

        $registeredWorkshopsQuery = WorkshopRegistration::where('alumni_user_id', $userId);
        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $registeredWorkshopsQuery->where('status', 'registered');
        }

        $jobApplicationsCount = JobApplication::where('alumni_user_id', $userId)->count();
        $scholarshipApplicationsCount = ScholarshipApplication::where('alumni_user_id', $userId)->count();
        $registeredWorkshopsCount = (clone $registeredWorkshopsQuery)->count();

        return [
            'jobBadgeCount' => (clone $jobsQuery)->count(),
            'workshopBadgeCount' => (clone $workshopsQuery)->count(),
            'recommendationsReceived' => Recommendation::where('to_user_id', $userId)->count(),
            'applicationsBadgeCount' => $jobApplicationsCount + $scholarshipApplicationsCount + $registeredWorkshopsCount,
        ];
    }
}
