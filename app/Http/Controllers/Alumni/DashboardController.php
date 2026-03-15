<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\ScholarshipApplication;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = (int) $user->id;

        $profile = AlumniProfile::where('user_id', $userId)->first();

        $profileCompletion = $this->calculateProfileCompletion($user, $profile);

        $profileViews = 0;
        if ($profile && Schema::hasColumn('alumni_profiles', 'profile_views')) {
            $profileViews = (int) ($profile->profile_views ?? 0);
        }

        $navBadges = $this->getNavBadges($userId);

        $leaderboardData = $this->buildLeaderboardData();

        $myLeaderboard = $leaderboardData->firstWhere('user_id', $userId);
        $leaderboardRank = $myLeaderboard['rank'] ?? null;
        $leaderboardPoints = $myLeaderboard['points'] ?? 0;
        $leaderboardActivities = $myLeaderboard['activities'] ?? 0;

        $jobsQuery = Job::query()->orderByDesc('id');

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }

        if (Schema::hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }

        $recentJobs = (clone $jobsQuery)->take(3)->get()->map(function ($job) {
            $job->posted_text = $job->created_at
                ? $job->created_at->diffForHumans()
                : ($job->posted ?? 'Recently');

            return $job;
        });

        $workshopsQuery = Workshop::query()->orderByDesc('id');

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshopsQuery->where('proposal_status', 'approved');
        }

        if (Schema::hasColumn('workshops', 'status')) {
            $workshopsQuery->where('status', 'upcoming');
        }

        $upcomingWorkshops = (clone $workshopsQuery)->take(2)->get();

        $topLeaderboard = $leaderboardData->take(3)->map(function ($item) {
            return [
                'rank' => $item['rank'],
                'name' => $item['name'],
                'points' => $item['points'],
                'avatar' => $item['initials'],
            ];
        })->values();

        $notifications = collect($user->unreadNotifications()->latest()->take(3)->get())
            ->map(function ($notification) {
                $data = is_array($notification->data) ? $notification->data : [];

                return [
                    'message' => $data['message'] ?? $data['title'] ?? 'New update available',
                    'time' => $notification->created_at?->diffForHumans() ?? 'Recently',
                ];
            })
            ->values()
            ->all();

        if (empty($notifications)) {
            $notifications = [
                ['message' => 'No new notifications yet.', 'time' => ''],
            ];
        }

        return view('alumni.index', [
            'userName' => $user->name ?? 'Alumni',
            'profileViews' => $profileViews,
            'profileCompletion' => $profileCompletion,
            'jobApplicationsCount' => $navBadges['jobApplicationsCount'],
            'workshopsCount' => $navBadges['registeredWorkshopsCount'],
            'leaderboardPoints' => $leaderboardPoints,
            'leaderboardRank' => $leaderboardRank,
            'leaderboardActivities' => $leaderboardActivities,
            'recentJobs' => $recentJobs,
            'upcomingWorkshops' => $upcomingWorkshops,
            'notifications' => $notifications,
            'topLeaderboard' => $topLeaderboard,

            'jobBadgeCount' => $navBadges['jobBadgeCount'],
            'workshopBadgeCount' => $navBadges['workshopBadgeCount'],
            'recommendationsReceived' => $navBadges['recommendationsReceived'],
            'applicationsBadgeCount' => $navBadges['applicationsBadgeCount'],
        ]);
    }

    private function getNavBadges(int $userId): array
    {
        $jobsQuery = Job::query();

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }

        if (Schema::hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }

        $workshopsQuery = Workshop::query();

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshopsQuery->where('proposal_status', 'approved');
        }

        if (Schema::hasColumn('workshops', 'status')) {
            $workshopsQuery->where('status', 'upcoming');
        }

        $registeredWorkshopsQuery = WorkshopRegistration::where('alumni_user_id', $userId);
        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $registeredWorkshopsQuery->where('status', 'registered');
        }

        $jobApplicationsCount = JobApplication::where('alumni_user_id', $userId)->count();
        $scholarshipApplicationsCount = class_exists(\App\Models\ScholarshipApplication::class)
            ? ScholarshipApplication::where('alumni_user_id', $userId)->count()
            : 0;
        $registeredWorkshopsCount = (clone $registeredWorkshopsQuery)->count();

        return [
            'jobBadgeCount' => (clone $jobsQuery)->count(),
            'workshopBadgeCount' => (clone $workshopsQuery)->count(),
            'recommendationsReceived' => Recommendation::where('to_user_id', $userId)->count(),
            'jobApplicationsCount' => $jobApplicationsCount,
            'registeredWorkshopsCount' => $registeredWorkshopsCount,
            'applicationsBadgeCount' => $jobApplicationsCount + $scholarshipApplicationsCount + $registeredWorkshopsCount,
        ];
    }

    private function buildLeaderboardData()
    {
        return User::query()
            ->where('role', 'alumni')
            ->get()
            ->map(function ($user) {
                $applicationsCount = JobApplication::where('alumni_user_id', $user->id)->count();
                $workshopsCount = WorkshopRegistration::where('alumni_user_id', $user->id)->count();
                $givenRecommendations = Recommendation::where('from_user_id', $user->id)->count();
                $receivedRecommendations = Recommendation::where('to_user_id', $user->id)->count();

                $activities = $applicationsCount + $workshopsCount + $givenRecommendations + $receivedRecommendations;

                $points =
                    ($applicationsCount * 20) +
                    ($workshopsCount * 30) +
                    ($givenRecommendations * 10) +
                    ($receivedRecommendations * 15);

                $name = $user->name ?? 'Alumni';
                $initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('');

                return [
                    'user_id' => (int) $user->id,
                    'name' => $name,
                    'initials' => $initials ?: 'A',
                    'points' => $points,
                    'activities' => $activities,
                ];
            })
            ->sortByDesc('points')
            ->values()
            ->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            });
    }

    private function calculateProfileCompletion($user, ?AlumniProfile $profile): int
    {
        $checks = [
            !empty($user?->name),
            !empty($user?->email),
            !empty($user?->academic_id),
            !empty($profile?->phone),
            !empty($profile?->location),
            !empty($profile?->major),
            !empty($profile?->graduation_year),
            !empty($profile?->bio),
            !empty($profile?->skills),
            !empty($profile?->linkedin),
            !empty($profile?->portfolio),
        ];

        $completed = collect($checks)->filter()->count();

        return (int) round(($completed / count($checks)) * 100);
    }
}
