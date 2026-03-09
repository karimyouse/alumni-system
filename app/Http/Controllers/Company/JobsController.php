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

        $jobs->getCollection()->transform(function ($job) {
            $job->display_status_label = $this->resolveStatusLabel($job);
            $job->display_status_class = $this->resolveStatusClass($job);
            $job->display_posted = $this->resolvePostedDate($job);
            $job->display_salary = trim((string) ($job->salary ?? ''));
            return $job;
        });

        return view('company.jobs', compact('jobs'));
    }

    public function create()
    {
        return view('company.job-create', [
            'job' => null,
            'isEdit' => false,
            'companyName' => Auth::user()?->name ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $companyId = Auth::id();

        $data = $this->validateJob($request);

        $attrs = [
            'company_user_id' => $companyId,
            'title' => $data['title'],
            'company_name' => $data['company_name'],
            'location' => $data['location'] ?: null,
            'type' => $data['type'] ?: null,
            'salary' => $data['salary'] ?: null,
            'description' => $data['description'] ?: null,
        ];

        if (Schema::hasColumn('jobs', 'status')) {
            $attrs['status'] = 'active';
        }

        if (Schema::hasColumn('jobs', 'views')) {
            $attrs['views'] = 0;
        }

        if (Schema::hasColumn('jobs', 'posted')) {
            $attrs['posted'] = now()->format('M d, Y');
        }

        $job = Job::create($attrs);

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $fill = ['approval_status' => 'pending'];

            if (Schema::hasColumn('jobs', 'approved_at')) {
                $fill['approved_at'] = null;
            }

            if (Schema::hasColumn('jobs', 'approved_by')) {
                $fill['approved_by'] = null;
            }

            if (Schema::hasColumn('jobs', 'reject_reason')) {
                $fill['reject_reason'] = null;
            }

            if (Schema::hasColumn('jobs', 'is_featured')) {
                $fill['is_featured'] = false;
            }

            $job->forceFill($fill)->save();

            return redirect()
                ->route('company.jobs')
                ->with('toast_success', 'Job submitted for college review.');
        }

        return redirect()
            ->route('company.jobs')
            ->with('toast_success', 'Job posted successfully.');
    }

    public function edit(Job $job)
    {
        $this->ensureOwner($job);

        return view('company.job-create', [
            'job' => $job,
            'isEdit' => true,
            'companyName' => Auth::user()?->name ?? '',
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $this->ensureOwner($job);

        $data = $this->validateJob($request);

        $update = [
            'title' => $data['title'],
            'company_name' => $data['company_name'],
            'location' => $data['location'] ?: null,
            'type' => $data['type'] ?: null,
            'salary' => $data['salary'] ?: null,
            'description' => $data['description'] ?: null,
        ];

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $update['approval_status'] = 'pending';

            if (Schema::hasColumn('jobs', 'approved_at')) {
                $update['approved_at'] = null;
            }

            if (Schema::hasColumn('jobs', 'approved_by')) {
                $update['approved_by'] = null;
            }

            if (Schema::hasColumn('jobs', 'reject_reason')) {
                $update['reject_reason'] = null;
            }
        }

        $job->update($update);

        return redirect()
            ->route('company.jobs')
            ->with('toast_success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->ensureOwner($job);

        $job->delete();

        return back()->with('toast_success', 'Job deleted successfully.');
    }

    public function applicants(Job $job)
    {
        $this->ensureOwner($job);

        $apps = JobApplication::with('alumni')
            ->where('job_id', $job->id)
            ->orderByDesc('id')
            ->get();

        return view('company.job-applicants', compact('job', 'apps'));
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
            'salary' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function ensureOwner(Job $job): void
    {
        if ((int) $job->company_user_id !== (int) Auth::id()) {
            abort(403);
        }
    }

    private function resolvePostedDate(Job $job): string
    {
        if (!empty($job->posted)) {
            return 'Posted ' . $job->posted;
        }

        if ($job->created_at) {
            return 'Posted ' . $job->created_at->format('M d, Y');
        }

        return 'Posted —';
    }

    private function resolveStatusLabel(Job $job): string
    {
        $approval = strtolower((string) ($job->approval_status ?? ''));

        if ($approval === 'pending') {
            return 'Pending Approval';
        }

        if ($approval === 'rejected') {
            return 'Rejected';
        }

        $status = strtolower((string) ($job->status ?? 'active'));

        return match ($status) {
            'closed' => 'Closed',
            default => 'Active',
        };
    }

    private function resolveStatusClass(Job $job): string
    {
        $approval = strtolower((string) ($job->approval_status ?? ''));

        if ($approval === 'pending') {
            return 'bg-secondary text-secondary-foreground';
        }

        if ($approval === 'rejected') {
            return 'bg-red-500/10 text-red-400';
        }

        $status = strtolower((string) ($job->status ?? 'active'));

        return match ($status) {
            'closed' => 'bg-red-500/10 text-red-400',
            default => 'bg-green-500/10 text-green-400',
        };
    }
}
