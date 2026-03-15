<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\SavedJob;
use App\Models\ScholarshipApplication;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $userId = (int) Auth::id();

        $jobsQuery = Job::query();

        if (Schema::hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }

        if ($q !== '') {
            $jobsQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        if (Schema::hasColumn('jobs', 'is_featured')) {
            $jobsQuery->orderByDesc('is_featured');
        }

        $jobs = $jobsQuery->orderByDesc('id')->paginate(10)->withQueryString();

        $appliedJobIds = JobApplication::where('alumni_user_id', $userId)
            ->pluck('job_id')
            ->toArray();

        $savedJobIds = SavedJob::where('alumni_user_id', $userId)
            ->pluck('job_id')
            ->toArray();

        $navBadges = $this->getNavBadges($userId);

        return view('alumni.jobs', [
            'jobs' => $jobs,
            'q' => $q,
            'appliedJobIds' => $appliedJobIds,
            'savedJobIds' => $savedJobIds,

            'workshopBadgeCount' => $navBadges['workshopBadgeCount'],
            'recommendationsReceived' => $navBadges['recommendationsReceived'],
            'applicationsBadgeCount' => $navBadges['applicationsBadgeCount'],
        ]);
    }

    public function show(Job $job)
    {
        if (Schema::hasColumn('jobs', 'status') && $job->status !== 'active') {
            abort(404);
        }

        if (Schema::hasColumn('jobs', 'approval_status') && ($job->approval_status ?? 'approved') !== 'approved') {
            abort(404);
        }

        $userId = Auth::id();

        $isApplied = JobApplication::where('alumni_user_id', $userId)
            ->where('job_id', $job->id)
            ->exists();

        $isSaved = SavedJob::where('alumni_user_id', $userId)
            ->where('job_id', $job->id)
            ->exists();

        return view('alumni.job-details', compact('job', 'isApplied', 'isSaved'));
    }

    public function toggleSave(Job $job)
    {
        $userId = Auth::id();

        if (Schema::hasColumn('jobs', 'status') && $job->status !== 'active') {
            return back()->with('toast_success', 'This job is not available.');
        }

        if (Schema::hasColumn('jobs', 'approval_status') && ($job->approval_status ?? 'approved') !== 'approved') {
            return back()->with('toast_success', 'This job is not approved yet.');
        }

        $exists = SavedJob::where('job_id', $job->id)
            ->where('alumni_user_id', $userId)
            ->exists();

        if ($exists) {
            SavedJob::where('job_id', $job->id)
                ->where('alumni_user_id', $userId)
                ->delete();

            return back()->with('toast_success', 'Removed from saved jobs.');
        }

        SavedJob::create([
            'job_id' => $job->id,
            'alumni_user_id' => $userId,
        ]);

        return back()->with('toast_success', 'Saved job successfully.');
    }

    public function apply(Job $job)
    {
        if (Schema::hasColumn('jobs', 'status') && $job->status !== 'active') {
            return back()->with('toast_success', 'This job is not available.');
        }

        if (Schema::hasColumn('jobs', 'approval_status') && ($job->approval_status ?? 'approved') !== 'approved') {
            return back()->with('toast_success', 'This job is not approved yet.');
        }

        JobApplication::updateOrCreate(
            ['job_id' => $job->id, 'alumni_user_id' => Auth::id()],
            [
                'status' => 'pending',
                'applied_date' => now()->format('M d, Y'),
            ]
        );

        return back()->with('toast_success', 'Successfully applied!');
    }

    private function getNavBadges(int $userId): array
    {
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
        $scholarshipApplicationsCount = class_exists(\App\Models\ScholarshipApplication::class)
            ? ScholarshipApplication::where('alumni_user_id', $userId)->count()
            : 0;
        $registeredWorkshopsCount = (clone $registeredWorkshopsQuery)->count();

        return [
            'workshopBadgeCount' => (clone $workshopsQuery)->count(),
            'recommendationsReceived' => Recommendation::where('to_user_id', $userId)->count(),
            'applicationsBadgeCount' => $jobApplicationsCount + $scholarshipApplicationsCount + $registeredWorkshopsCount,
        ];
    }
}
