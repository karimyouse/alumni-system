<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Models\LeaderboardEntry;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Cards (dynamic for any user)
        $profileViews = 48; // (Optional later: profile_views table)
        $jobApplicationsCount = JobApplication::where('alumni_user_id', $userId)->count();
        $workshopsCount = WorkshopRegistration::where('alumni_user_id', $userId)->count();

        // Ensure leaderboard entry exists for ANY alumni user
        $myEntry = LeaderboardEntry::firstOrCreate(
            ['alumni_user_id' => $userId, 'period' => 'monthly'],
            ['rank' => 0, 'points' => 0, 'activities' => 0, 'trend' => '+0']
        );

        // My rank (computed)
        $all = LeaderboardEntry::where('period', 'monthly')
            ->orderByDesc('points')
            ->orderByDesc('activities')
            ->orderBy('id')
            ->get(['alumni_user_id']);

        $pos = $all->search(fn($e) => (int)$e->alumni_user_id === (int)$userId);
        $leaderboardRank = ($pos !== false) ? $pos + 1 : null;

        $leaderboardPoints = (int)($myEntry->points ?? 0);

        // Latest jobs (dynamic)
        $recentJobs = Job::where('status', 'active')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Upcoming workshops (dynamic)
        $upcomingWorkshops = Workshop::orderByDesc('id')
            ->take(2)
            ->get();

        // Top 3 leaderboard (dynamic)
        $topLeaderboard = LeaderboardEntry::with('alumni')
            ->where('period', 'monthly')
            ->orderByDesc('points')
            ->orderByDesc('activities')
            ->orderBy('id')
            ->take(3)
            ->get()
            ->values()
            ->map(function ($e, $idx) {
                $name = $e->alumni?->name ?? 'Alumni';
                $avatar = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn($n)=>mb_substr($n, 0, 1))
                    ->join('');

                return [
                    'rank' => $idx + 1,
                    'name' => $name,
                    'points' => (int)$e->points,
                    'avatar' => $avatar ?: 'A',
                ];
            });

        // Notifications (temporary until you create notifications table)
        $notifications = [
            ['message' => 'New job opportunity matches your profile', 'time' => '1 hour ago'],
            ['message' => 'Workshop registration deadline tomorrow',  'time' => '3 hours ago'],
            ['message' => 'You received a new recommendation',        'time' => '1 day ago'],
        ];

        return view('alumni.index', [
            'userName' => Auth::user()?->name ?? 'Alumni',
            'profileViews' => $profileViews,
            'jobApplicationsCount' => $jobApplicationsCount,
            'workshopsCount' => $workshopsCount,
            'leaderboardPoints' => $leaderboardPoints,
            'leaderboardRank' => $leaderboardRank,
            'recentJobs' => $recentJobs,
            'upcomingWorkshops' => $upcomingWorkshops,
            'notifications' => $notifications,
            'topLeaderboard' => $topLeaderboard,
        ]);
    }
}
