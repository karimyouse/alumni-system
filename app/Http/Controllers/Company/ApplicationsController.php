<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationsController extends Controller
{
    private function mapStatus(string $raw): array
    {
        $raw = strtolower(trim($raw));

        return match ($raw) {
            'pending'  => ['Pending', 'bg-muted text-foreground'],
            'reviewed' => ['Under Review', 'bg-blue-500/15 text-blue-400'],
            'accepted' => ['Accepted', 'bg-green-500/15 text-green-400'],
            'rejected' => ['Rejected', 'bg-red-500/15 text-red-400'],
            default    => [ucfirst($raw), 'bg-secondary text-secondary-foreground'],
        };
    }

    public function index(Request $request)
    {
        $companyId = Auth::id();
        $tab = $request->query('tab', 'all');

        $jobIds = Job::query()
            ->where('organizer_role', 'company')
            ->where('company_user_id', $companyId)
            ->pluck('id');

        $appsAll = JobApplication::with(['job', 'alumni'])
            ->whereIn('job_id', $jobIds)
            ->orderByDesc('id')
            ->get();

        $counts = [
            'all'      => $appsAll->count(),
            'pending'  => $appsAll->where('status', 'pending')->count(),
            'reviewed' => $appsAll->where('status', 'reviewed')->count(),
            'accepted' => $appsAll->where('status', 'accepted')->count(),
            'rejected' => $appsAll->where('status', 'rejected')->count(),
        ];

        $apps = match ($tab) {
            'pending', 'reviewed', 'accepted', 'rejected' => $appsAll->where('status', $tab)->values(),
            default => $appsAll->values(),
        };

        $items = $apps->map(function ($a) {
            [$label, $class] = $this->mapStatus($a->status ?? 'pending');

            return [
                'id' => $a->id,
                'job_title' => $a->job?->title ?? 'Job',
                'company_name' => $a->job?->company_name ?? 'Company',
                'applicant_name' => $a->alumni?->name ?? 'Alumni',
                'applicant_email' => $a->alumni?->email ?? '',
                'academic_id' => $a->alumni?->academic_id ?? '',
                'applied_at' => $a->applied_date ?: ($a->created_at?->format('M d, Y') ?? ''),
                'status' => $a->status ?? 'pending',
                'status_label' => $label,
                'status_class' => $class,
            ];
        })->values();

        return view('company.applications', array_merge(
            compact('tab', 'counts', 'items'),
            $this->buildNavCounts()
        ));
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        $application->load('job');

        if ((int) ($application->job?->company_user_id) !== (int) Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,reviewed,accepted,rejected'],
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('toast_success', 'Application status updated.');
    }

    private function buildNavCounts(): array
    {
        $companyId = (int) Auth::id();

        $companyJobsQuery = Job::query()
            ->where('organizer_role', 'company')
            ->where('company_user_id', $companyId);

        $jobIds = (clone $companyJobsQuery)->pluck('id');

        return [
            'jobBadgeCount' => (clone $companyJobsQuery)->count(),
            'alumniBadgeCount' => User::where('role', 'alumni')->count(),
            'applicationBadgeCount' => JobApplication::whereIn('job_id', $jobIds)->count(),
            'workshopBadgeCount' => Workshop::where('company_user_id', $companyId)->count(),
        ];
    }
}
