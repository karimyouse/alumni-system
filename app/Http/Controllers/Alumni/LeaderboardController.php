<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardEntry;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ensure entry exists لأي alumni
        $myEntry = LeaderboardEntry::firstOrCreate(
            ['alumni_user_id' => $userId, 'period' => 'monthly'],
            ['rank' => 0, 'points' => 0, 'activities' => 0, 'trend' => '+0']
        );

        $entries = LeaderboardEntry::with('alumni')
            ->where('period', 'monthly')
            ->orderByDesc('points')
            ->orderByDesc('activities')
            ->orderBy('id')
            ->get();

        $ranked = $entries->values()->map(function ($e, $idx) use ($userId) {
            $e->computed_rank = $idx + 1;
            $e->is_me = ((int)$e->alumni_user_id === (int)$userId);
            return $e;
        });

        $myRank = optional($ranked->firstWhere('alumni_user_id', $userId))->computed_rank ?? null;

        return view('alumni.leaderboard', [
            'ranked' => $ranked,
            'myEntry' => $myEntry,
            'myRank' => $myRank,
        ]);
    }
}
