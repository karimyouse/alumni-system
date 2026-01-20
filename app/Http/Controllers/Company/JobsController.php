<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobsController extends Controller
{
    public function index()
    {
        $companyId = Auth::id();

        $jobs = Job::where('company_user_id', $companyId)
            ->orderByDesc('id')
            ->paginate(10);

        return view('company.jobs', compact('jobs'));
    }

    public function create()
    {
        return view('company.job-create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::id();

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'company_name' => ['required','string','max:255'],
            'location' => ['nullable','string','max:255'],
            'type' => ['nullable','string','max:50'],
            'salary' => ['nullable','string','max:50'],
            'description' => ['nullable','string','max:5000'],
        ]);

        Job::create([
            'company_user_id' => $companyId,
            'title' => $data['title'],
            'company_name' => $data['company_name'],
            'location' => $data['location'] ?? null,
            'type' => $data['type'] ?? null,
            'salary' => $data['salary'] ?? null,
            'posted' => 'Just now',
            'description' => $data['description'] ?? null,
            'status' => 'active',
            'views' => 0,
        ]);

        return redirect()->route('company.jobs')->with('toast_success', 'Job posted successfully!');
    }

    public function applicants(Job $job)
    {
        if ((int)$job->company_user_id !== (int)Auth::id()) {
            abort(403);
        }

        $apps = JobApplication::with('alumni')
            ->where('job_id', $job->id)
            ->orderByDesc('id')
            ->get();

        return view('company.job-applicants', compact('job', 'apps'));
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        $application->load('job');

        if ((int)($application->job?->company_user_id) !== (int)Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required','in:pending,reviewed,accepted,rejected'],
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('toast_success', 'Application status updated.');
    }
}
