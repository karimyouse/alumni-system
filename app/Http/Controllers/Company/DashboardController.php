<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Auth::user();
        $companyId = (int) $company->id;

        $jobsQuery = Job::query()->where('company_user_id', $companyId);

        $jobsCount = (clone $jobsQuery)->count();
        $activeJobsCount = (clone $jobsQuery)->where('status', 'active')->count();

        $jobIds = (clone $jobsQuery)->pluck('id');

        $applicationsQuery = JobApplication::query()->whereIn('job_id', $jobIds);

        $applicationsCount = (clone $applicationsQuery)->count();
        $pendingCount = (clone $applicationsQuery)->where('status', 'pending')->count();
        $reviewedCount = (clone $applicationsQuery)->where('status', 'reviewed')->count();
        $acceptedCount = (clone $applicationsQuery)->where('status', 'accepted')->count();
        $rejectedCount = (clone $applicationsQuery)->where('status', 'rejected')->count();

        $profileViews = 0;
        if (Schema::hasColumn('jobs', 'views')) {
            $profileViews = (int) ((clone $jobsQuery)->sum('views') ?? 0);
        }

        $candidatesViewed = (clone $applicationsQuery)
            ->distinct('alumni_user_id')
            ->count('alumni_user_id');

        $latestJobs = (clone $jobsQuery)
            ->orderByDesc('id')
            ->take(3)
            ->get()
            ->map(function ($job) {
                $job->applications_count = JobApplication::where('job_id', $job->id)->count();
                $job->display_views = Schema::hasColumn('jobs', 'views') ? (int) ($job->views ?? 0) : 0;
                $job->display_posted = $job->posted ?: ($job->created_at ? $job->created_at->format('M j, Y') : '—');
                $job->display_status = ucfirst((string) ($job->status ?? 'active'));
                return $job;
            });

        $recentApplications = (clone $applicationsQuery)
            ->with(['alumni', 'job'])
            ->orderByDesc('id')
            ->take(4)
            ->get()
            ->map(function ($application) {
                $name = $application->alumni?->name ?? 'Alumni';
                $application->candidate_initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('') ?: 'A';

                $application->candidate_name = $name;
                $application->job_title = $application->job?->title ?? 'Job';
                $application->display_status = ucfirst((string) ($application->status ?? 'pending'));

                $application->status_class = match ($application->status) {
                    'accepted' => 'bg-green-500/10 text-green-400',
                    'rejected' => 'bg-red-500/10 text-red-400',
                    'reviewed' => 'bg-blue-500/10 text-blue-400',
                    default => 'bg-yellow-500/10 text-yellow-400',
                };

                return $application;
            });

        $recommendedCandidates = User::query()
            ->where('role', 'alumni')
            ->with('alumniProfile')
            ->get()
            ->map(function ($user) {
                $profile = $user->alumniProfile;

                $skills = collect(explode(',', (string) ($profile->skills ?? '')))
                    ->map(fn ($s) => trim($s))
                    ->filter()
                    ->values();

                $completionChecks = [
                    !empty($user->name),
                    !empty($user->email),
                    !empty($user->academic_id),
                    !empty($profile?->phone),
                    !empty($profile?->location),
                    !empty($profile?->major),
                    !empty($profile?->graduation_year),
                    !empty($profile?->bio),
                    $skills->isNotEmpty(),
                    !empty($profile?->linkedin),
                    !empty($profile?->portfolio),
                ];

                $completed = collect($completionChecks)->filter()->count();
                $completionPercent = (int) round(($completed / count($completionChecks)) * 100);

                $initials = collect(explode(' ', (string) $user->name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('') ?: 'A';

                return [
                    'id' => $user->id,
                    'name' => $user->name ?? 'Alumni',
                    'initials' => $initials,
                    'graduation_year' => $profile->graduation_year ?? '—',
                    'skills' => $skills->take(3)->values(),
                    'match' => min($completionPercent, 99),
                ];
            })
            ->sortByDesc('match')
            ->take(3)
            ->values();

        return view('company.index', [
            'companyName' => $company->name ?? 'Company',
            'jobsCount' => $jobsCount,
            'activeJobsCount' => $activeJobsCount,
            'applicationsCount' => $applicationsCount,
            'pendingCount' => $pendingCount,
            'reviewedCount' => $reviewedCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'profileViews' => $profileViews,
            'candidatesViewed' => $candidatesViewed,
            'latestJobs' => $latestJobs,
            'recentApplications' => $recentApplications,
            'recommendedCandidates' => $recommendedCandidates,
        ]);
    }
}
