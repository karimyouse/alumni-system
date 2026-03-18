<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Scholarship;
use App\Models\SuccessStory;
use App\Models\User;
use App\Models\Workshop;
use App\Notifications\ContentReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $q = trim((string) $request->query('q', ''));

        $query = Job::query()
            ->with('company')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                  ->orWhere('company_name', 'like', "%{$q}%")
                  ->orWhere('location', 'like', "%{$q}%");
            });
        }

        if (Schema::hasColumn('jobs', 'approval_status') && $status !== 'all') {
            $query->where('approval_status', $status);
        }

        $jobs = $query->paginate(10)->withQueryString();

        $jobs->getCollection()->transform(function ($job) {
            $job->is_company_submission = $this->isCompanySubmittedJob($job);

            $job->display_owner_name = $job->is_company_submission
                ? ($job->company?->name ?? ($job->company_name ?: 'Company'))
                : 'PTC College';

            $appsQuery = JobApplication::query()->where('job_id', $job->id);

            $job->display_applicants_count = (clone $appsQuery)->count();
            $job->display_accepted_count = (clone $appsQuery)->where('status', 'accepted')->count();

            return $job;
        });

        $counts = [
            'all' => Job::count(),
            'approved' => Schema::hasColumn('jobs', 'approval_status')
                ? Job::where('approval_status', 'approved')->count()
                : 0,
            'pending' => Schema::hasColumn('jobs', 'approval_status')
                ? Job::where('approval_status', 'pending')->count()
                : 0,
            'rejected' => Schema::hasColumn('jobs', 'approval_status')
                ? Job::where('approval_status', 'rejected')->count()
                : 0,
        ];

        return view('college.jobs', array_merge(
            compact('jobs', 'status', 'q', 'counts'),
            $this->buildNavCounts()
        ));
    }

    public function create()
    {
        return view('college.jobs.create', array_merge([
            'job' => null,
            'isEdit' => false,
            'companyName' => 'PTC College',
        ], $this->buildNavCounts()));
    }

    public function store(Request $request)
    {
        $data = $this->validateJob($request);

        $attrs = [
            'company_user_id' => null,
            'title' => $data['title'],
            'company_name' => $data['company_name'] ?: 'PTC College',
            'location' => $data['location'] ?: null,
            'type' => $data['type'] ?: null,
            'salary' => $data['salary'] ?: null,
            'description' => $data['description'] ?: null,
        ];

        if (Schema::hasColumn('jobs', 'organizer_user_id')) {
            $attrs['organizer_user_id'] = Auth::id();
        }

        if (Schema::hasColumn('jobs', 'organizer_role')) {
            $attrs['organizer_role'] = 'college';
        }

        if (Schema::hasColumn('jobs', 'status')) {
            $attrs['status'] = 'active';
        }

        if (Schema::hasColumn('jobs', 'posted')) {
            $attrs['posted'] = now()->format('M d, Y');
        }

        if (Schema::hasColumn('jobs', 'views')) {
            $attrs['views'] = 0;
        }

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $attrs['approval_status'] = 'approved';
        }

        if (Schema::hasColumn('jobs', 'approved_at')) {
            $attrs['approved_at'] = now();
        }

        if (Schema::hasColumn('jobs', 'approved_by')) {
            $attrs['approved_by'] = Auth::id();
        }

        if (Schema::hasColumn('jobs', 'reject_reason')) {
            $attrs['reject_reason'] = null;
        }

        if (Schema::hasColumn('jobs', 'is_featured')) {
            $attrs['is_featured'] = false;
        }

        $job = Job::create($attrs);

        $this->notifyAlumniAboutCollegeJob($job);

        return redirect()
            ->route('college.jobs')
            ->with('toast_success', 'Job created successfully.');
    }

    public function edit(Job $job)
    {
        $this->ensureCollegeOwnsJob($job);

        return view('college.jobs.create', array_merge([
            'job' => $job,
            'isEdit' => true,
            'companyName' => 'PTC College',
        ], $this->buildNavCounts()));
    }

    public function update(Request $request, Job $job)
    {
        $this->ensureCollegeOwnsJob($job);

        $data = $this->validateJob($request);

        $update = [
            'title' => $data['title'],
            'company_name' => $data['company_name'] ?: 'PTC College',
            'location' => $data['location'] ?: null,
            'type' => $data['type'] ?: null,
            'salary' => $data['salary'] ?: null,
            'description' => $data['description'] ?: null,
        ];

        if (Schema::hasColumn('jobs', 'organizer_user_id') && empty($job->organizer_user_id)) {
            $update['organizer_user_id'] = Auth::id();
        }

        if (Schema::hasColumn('jobs', 'organizer_role')) {
            $update['organizer_role'] = 'college';
        }

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $update['approval_status'] = 'approved';
        }

        if (Schema::hasColumn('jobs', 'approved_at')) {
            $update['approved_at'] = now();
        }

        if (Schema::hasColumn('jobs', 'approved_by')) {
            $update['approved_by'] = Auth::id();
        }

        if (Schema::hasColumn('jobs', 'reject_reason')) {
            $update['reject_reason'] = null;
        }

        $job->update($update);

        return redirect()
            ->route('college.jobs')
            ->with('toast_success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->ensureCollegeOwnsJob($job);

        $job->delete();

        return back()->with('toast_success', 'Job deleted successfully.');
    }

    public function approve(Job $job)
    {
        $this->ensureCompanySubmittedJob($job);

        $job->forceFill([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'reject_reason' => null,
        ])->save();

        $this->notifyCompany($job, true);

        return back()->with('toast_success', 'Job approved.');
    }

    public function reject(Request $request, Job $job)
    {
        $this->ensureCompanySubmittedJob($job);

        $data = $request->validate([
            'reject_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = $data['reject_reason'] ?? 'Rejected by college.';

        $job->forceFill([
            'approval_status' => 'rejected',
            'approved_at' => null,
            'approved_by' => Auth::id(),
            'reject_reason' => $reason,
        ])->save();

        $this->notifyCompany($job, false, $reason);

        return back()->with('toast_success', 'Job rejected.');
    }

    public function applicants(Job $job)
    {
        $apps = JobApplication::with('alumni')
            ->where('job_id', $job->id)
            ->orderByDesc('id')
            ->get();

        return view('college.job-applicants', array_merge(
            compact('job', 'apps'),
            $this->buildNavCounts()
        ));
    }

    public function updateApplicantStatus(Job $job, JobApplication $application, Request $request)
    {
    $this->ensureCollegeOwnsJob($job);

    if ((int) $application->job_id !== (int) $job->id) {
        abort(404);
    }

    $data = $request->validate([
        'status' => ['required', 'in:pending,reviewed,accepted,rejected'],
    ]);

    $application->update([
        'status' => $data['status'],
    ]);

    return back()->with('toast_success', 'Applicant status updated successfully.');
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
            'salary' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function isCompanySubmittedJob(Job $job): bool
    {
        if (($job->organizer_role ?? null) === 'company') {
            return true;
        }

        if (($job->organizer_role ?? null) === 'college') {
            return false;
        }

        return !is_null($job->company_user_id ?? null);
    }

    private function ensureCollegeOwnsJob(Job $job): void
    {
        if ($this->isCompanySubmittedJob($job)) {
            abort(403);
        }
    }

    private function ensureCompanySubmittedJob(Job $job): void
    {
        if (!$this->isCompanySubmittedJob($job)) {
            abort(403);
        }
    }

    private function notifyCompany(Job $job, bool $approved, ?string $reason = null): void
    {
        try {
            $company = $job->company;
            if (!$company) {
                return;
            }

            $company->notify(new ContentReviewNotification([
                'kind' => 'content_review',
                'content_type' => 'job',
                'content_id' => $job->id,
                'status' => $approved ? 'approved' : 'rejected',
                'title' => $approved ? 'Your job was approved' : 'Your job was rejected',
                'message' => $approved
                    ? 'Your job "' . $job->title . '" has been approved and is now visible to alumni.'
                    : 'Your job "' . $job->title . '" was rejected.' . ($reason ? ' Reason: ' . $reason : ''),
                'icon' => 'briefcase',
                'admin_note' => $reason,
                'url' => route('company.jobs'),
            ]));
        } catch (\Throwable $e) {
        }
    }

    private function notifyAlumniAboutCollegeJob(Job $job): void
    {
        try {
            $alumniUsers = User::query()
                ->where('role', 'alumni')
                ->get();

            foreach ($alumniUsers as $alumnus) {
                $alumnus->notify(new ContentReviewNotification([
                    'kind' => 'content_review',
                    'content_type' => 'job',
                    'content_id' => $job->id,
                    'status' => 'approved',
                    'title' => 'New job available',
                    'message' => '"' . $job->title . '" has been posted by the college and is now available.',
                    'icon' => 'briefcase',
                    'url' => route('alumni.jobs'),
                ]));
            }
        } catch (\Throwable $e) {
        }
    }

    private function buildNavCounts(): array
    {
        return [
            'alumniBadgeCount' => User::where('role', 'alumni')->count(),
            'workshopBadgeCount' => Workshop::count(),
            'jobBadgeCount' => Job::count(),
            'announcementBadgeCount' => Announcement::count(),
            'scholarshipBadgeCount' => Scholarship::count(),
            'successStoryBadgeCount' => SuccessStory::count(),
        ];
    }
}
