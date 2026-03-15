<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Workshop;
use App\Notifications\ContentReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index()
    {
        $companyId = Auth::id();

        $jobs = Job::query()
            ->where('organizer_role', 'company')
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

        return view('company.jobs', array_merge(
            compact('jobs'),
            $this->buildNavCounts()
        ));
    }

    public function create()
    {
        return view('company.job-create', array_merge([
            'job' => null,
            'isEdit' => false,
            'companyName' => Auth::user()?->name ?? '',
        ], $this->buildNavCounts()));
    }

    public function store(Request $request)
    {
        $companyId = Auth::id();
        $data = $this->validateJob($request);

        $attrs = [
            'company_user_id' => $companyId,
            'organizer_user_id' => $companyId,
            'organizer_role' => 'company',
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

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $attrs['approval_status'] = 'pending';
        }

        if (Schema::hasColumn('jobs', 'approved_at')) {
            $attrs['approved_at'] = null;
        }

        if (Schema::hasColumn('jobs', 'approved_by')) {
            $attrs['approved_by'] = null;
        }

        if (Schema::hasColumn('jobs', 'reject_reason')) {
            $attrs['reject_reason'] = null;
        }

        if (Schema::hasColumn('jobs', 'is_featured')) {
            $attrs['is_featured'] = false;
        }

        $job = Job::create($attrs);

        $this->notifyCollegesAboutJob($job);

        return redirect()
            ->route('company.jobs')
            ->with('toast_success', 'Job submitted for college review.');
    }

    public function edit(Job $job)
    {
        $this->ensureOwner($job);

        return view('company.job-create', array_merge([
            'job' => $job,
            'isEdit' => true,
            'companyName' => Auth::user()?->name ?? '',
        ], $this->buildNavCounts()));
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
        }

        if (Schema::hasColumn('jobs', 'approved_at')) {
            $update['approved_at'] = null;
        }

        if (Schema::hasColumn('jobs', 'approved_by')) {
            $update['approved_by'] = null;
        }

        if (Schema::hasColumn('jobs', 'reject_reason')) {
            $update['reject_reason'] = null;
        }

        if (Schema::hasColumn('jobs', 'is_featured')) {
            $update['is_featured'] = false;
        }

        $job->update($update);

        $this->notifyCollegesAboutJob($job, true);

        return redirect()
            ->route('company.jobs')
            ->with('toast_success', 'Job updated and re-submitted for college review.');
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

        return view('company.job-applicants', array_merge(
            compact('job', 'apps'),
            $this->buildNavCounts()
        ));
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
        if (($job->organizer_role ?? null) !== 'company' || (int) $job->company_user_id !== (int) Auth::id()) {
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

        if ($approval === 'pending') return 'Pending Approval';
        if ($approval === 'rejected') return 'Rejected';
        if ($approval === 'approved') return 'Approved';

        $status = strtolower((string) ($job->status ?? 'active'));

        return match ($status) {
            'closed' => 'Closed',
            default => 'Active',
        };
    }

    private function resolveStatusClass(Job $job): string
    {
        $approval = strtolower((string) ($job->approval_status ?? ''));

        if ($approval === 'pending') return 'bg-secondary text-secondary-foreground';
        if ($approval === 'rejected') return 'bg-red-500/10 text-red-400';
        if ($approval === 'approved') return 'bg-green-500/10 text-green-400';

        $status = strtolower((string) ($job->status ?? 'active'));

        return match ($status) {
            'closed' => 'bg-red-500/10 text-red-400',
            default => 'bg-green-500/10 text-green-400',
        };
    }

    private function notifyCollegesAboutJob(Job $job, bool $updated = false): void
    {
        try {
            $colleges = User::query()->where('role', 'college')->get();
            $companyName = Auth::user()?->name ?? ($job->company_name ?? 'Company');

            foreach ($colleges as $college) {
                $college->notify(new ContentReviewNotification([
                    'kind' => 'content_review',
                    'content_type' => 'job',
                    'content_id' => $job->id,
                    'status' => 'pending',
                    'title' => $updated ? 'Company updated a job for review' : 'New company job needs review',
                    'message' => $companyName . ' submitted "' . $job->title . '" for review.',
                    'icon' => 'briefcase',
                    'url' => route('college.jobs', ['status' => 'pending']),
                ]));
            }
        } catch (\Throwable $e) {
        }
    }

    private function buildNavCounts(): array
    {
    $companyId = Auth::id();

    $jobsQuery = Job::query()
        ->where('company_user_id', $companyId);

    if (Schema::hasColumn('jobs', 'organizer_role')) {
        $jobsQuery->where('organizer_role', 'company');
    }

    $jobIds = (clone $jobsQuery)->pluck('id');

    $applicationsQuery = JobApplication::query()->whereIn('job_id', $jobIds);

    $workshopsQuery = Workshop::query()->where('company_user_id', $companyId);

    if (Schema::hasColumn('workshops', 'organizer_role')) {
        $workshopsQuery->where('organizer_role', 'company');
    }

    return [
        'jobBadgeCount' => (clone $jobsQuery)->count(),
        'alumniBadgeCount' => User::where('role', 'alumni')->count(),
        'applicationBadgeCount' => (clone $applicationsQuery)->count(),
        'workshopBadgeCount' => (clone $workshopsQuery)->count(),
    ];
    }
}
