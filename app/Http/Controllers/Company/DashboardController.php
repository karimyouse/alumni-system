<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::id();

        $jobsCount = Job::where('company_user_id', $companyId)->count();
        $activeJobsCount = Job::where('company_user_id', $companyId)->where('status', 'active')->count();

        $jobIds = Job::where('company_user_id', $companyId)->pluck('id');

        $applicationsCount = JobApplication::whereIn('job_id', $jobIds)->count();
        $pendingCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'pending')->count();
        $reviewedCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'reviewed')->count();
        $acceptedCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'accepted')->count();
        $rejectedCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'rejected')->count();

        $latestJobs = Job::where('company_user_id', $companyId)
            ->orderByDesc('id')->take(5)->get();

        return view('company.index', compact(
            'jobsCount','activeJobsCount',
            'applicationsCount','pendingCount','reviewedCount','acceptedCount','rejectedCount',
            'latestJobs'
        ));
    }
}
