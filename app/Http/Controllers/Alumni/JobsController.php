<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;

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

        $appliedJobIds = JobApplication::where('alumni_user_id', auth()->id())
            ->pluck('job_id')
            ->toArray();

        return view('alumni.jobs', compact('jobs', 'q', 'appliedJobIds'));
    }

    public function apply(Job $job)
    {

    if ($job->status !== 'active') {
            return back()->with('toast_success', 'This job is not available.');
        }

        JobApplication::updateOrCreate(
            ['job_id' => $job->id, 'alumni_user_id' => auth()->id()],
            ['status' => 'pending', 'applied_date' => now()->format('M d, Y')]
        );

        return back()->with('toast_success', 'Successfully applied!');
    }
}
