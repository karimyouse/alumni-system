<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index()
    {
        $currentUserId = (int) Auth::id();

        $ranked = User::query()
            ->where('role', 'alumni')
            ->get()
            ->map(function ($user) use ($currentUserId) {
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

                $name = $user->name ?? __('Alumni');
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
                    'applications_count' => $applicationsCount,
                    'workshops_count' => $workshopsCount,
                    'given_recommendations' => $givenRecommendations,
                    'received_recommendations' => $receivedRecommendations,
                    'is_me' => (int) $user->id === $currentUserId,
                ];
            })
            ->sortByDesc('points')
            ->values()
            ->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            });

        $topThree = $ranked->take(3)->values();
        $myRank = optional($ranked->firstWhere('is_me', true))['rank'] ?? null;

        return view('alumni.leaderboard', [
            'ranked' => $ranked,
            'topThree' => $topThree,
            'myRank' => $myRank,
        ]);
    }
}
