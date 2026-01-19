<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedJob;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $jobsQuery = Job::query()
            ->where('status', 'active')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%")
                        ->orWhere('type', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id');

        $jobs = $jobsQuery->paginate(10)->withQueryString();

        $appliedJobIds = JobApplication::where('alumni_user_id', Auth::id())
            ->pluck('job_id')
            ->toArray();

        $savedJobIds = SavedJob::where('alumni_user_id', Auth::id())
            ->pluck('job_id')
            ->toArray();


        return view('alumni.jobs', compact('jobs', 'q', 'appliedJobIds', 'savedJobIds'));
    }

    public function toggleSave(Job $job)
    {
    $userId = Auth::id();

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

    if ($job->status !== 'active') {
            return back()->with('toast_success', 'This job is not available.');
        }

        JobApplication::updateOrCreate(
            ['job_id' => $job->id, 'alumni_user_id' => Auth::id()],
            ['status' => 'pending', 'applied_date' => now()->format('M d, Y')]
        );

        return back()->with('toast_success', 'Successfully applied!');
    }
}
