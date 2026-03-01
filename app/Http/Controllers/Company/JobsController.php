<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index()
    {
        $companyId = Auth::id();

        $jobs = Job::query()
            ->where('company_user_id', $companyId)
            ->withCount('applications')
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

        $attrs = [
            'company_user_id' => $companyId,
            'title' => $data['title'],
            'company_name' => $data['company_name'],
            'location' => $data['location'] ?? null,
            'type' => $data['type'] ?? null,
            'salary' => $data['salary'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        // optional columns
        if (Schema::hasColumn('jobs', 'status')) $attrs['status'] = 'active';
        if (Schema::hasColumn('jobs', 'views')) $attrs['views'] = 0;
        if (Schema::hasColumn('jobs', 'posted')) $attrs['posted'] = now()->format('M d, Y');

        // ✅ create first (in case fillable isn't perfect)
        $job = Job::create($attrs);

        // ✅ GUARANTEED: set review fields using forceFill (works even if fillable misses them)
        if (Schema::hasColumn('jobs', 'approval_status')) {
            $job->forceFill([
                'approval_status' => 'pending',
                'approved_at' => Schema::hasColumn('jobs', 'approved_at') ? null : null,
                'approved_by' => Schema::hasColumn('jobs', 'approved_by') ? null : null,
                'reject_reason' => Schema::hasColumn('jobs', 'reject_reason') ? null : null,
                'is_featured' => Schema::hasColumn('jobs', 'is_featured') ? false : ($job->is_featured ?? false),
            ])->save();

            return redirect()->route('company.jobs')
                ->with('toast_success', 'Job submitted for college review (Pending).');
        }

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

    /**
     * NOTE:
     * الأفضل تحديث status للطلبات من CompanyApplicationsController
     * لكن لو لسه عندك فورم update داخل صفحة Applicants فهذا سيبقى شغال.
     */
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
