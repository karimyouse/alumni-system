<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\SavedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

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
        $userId = Auth::id();

        $appliedJobIds = JobApplication::where('alumni_user_id', $userId)
            ->pluck('job_id')
            ->toArray();

        $savedJobIds = SavedJob::where('alumni_user_id', $userId)
            ->pluck('job_id')
            ->toArray();

        return view('alumni.jobs', compact('jobs', 'q', 'appliedJobIds', 'savedJobIds'));
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
            return back()->with('toast_success', __('This job is not available.'));
        }

        if (Schema::hasColumn('jobs', 'approval_status') && ($job->approval_status ?? 'approved') !== 'approved') {
            return back()->with('toast_success', __('This job is not approved yet.'));
        }

        $exists = SavedJob::where('job_id', $job->id)
            ->where('alumni_user_id', $userId)
            ->exists();

        if ($exists) {
            SavedJob::where('job_id', $job->id)
                ->where('alumni_user_id', $userId)
                ->delete();

            return back()->with('toast_success', __('Removed from saved jobs.'));
        }

        SavedJob::create([
            'job_id' => $job->id,
            'alumni_user_id' => $userId,
        ]);

        return back()->with('toast_success', __('Saved job successfully.'));
    }

    public function apply(Job $job)
    {
        if (Schema::hasColumn('jobs', 'status') && $job->status !== 'active') {
            return back()->with('toast_success', __('This job is not available.'));
        }

        if (Schema::hasColumn('jobs', 'approval_status') && ($job->approval_status ?? 'approved') !== 'approved') {
            return back()->with('toast_success', __('This job is not approved yet.'));
        }

        JobApplication::updateOrCreate(
            ['job_id' => $job->id, 'alumni_user_id' => Auth::id()],
            [
                'status' => 'pending',
                'applied_date' => now()->format('M d, Y'),
            ]
        );

        return back()->with('toast_success', __('Successfully applied!'));
    }
}
