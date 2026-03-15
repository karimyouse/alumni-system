<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\ScholarshipApplication;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RecommendationsController extends Controller
{
    public function index()
    {
        $userId = (int) Auth::id();

        $received = Recommendation::query()
            ->where('to_user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                $name = $r->from_name ?: 'Alumni';
                $initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('');

                return (object) [
                    'id' => $r->id,
                    'name' => $name,
                    'initials' => $initials ?: 'A',
                    'role_title' => $r->role_title,
                    'content' => $r->content,
                    'date' => $r->date ?: optional($r->created_at)->format('M d, Y'),
                ];
            });

        $given = Recommendation::query()
            ->where('from_user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                $name = $r->to_name ?: 'Alumni';
                $initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('');

                return (object) [
                    'id' => $r->id,
                    'name' => $name,
                    'initials' => $initials ?: 'A',
                    'role_title' => $r->role_title,
                    'content' => $r->content,
                    'date' => $r->date ?: optional($r->created_at)->format('M d, Y'),
                ];
            });

        $alumniList = User::query()
            ->where('role', 'alumni')
            ->where('id', '!=', $userId)
            ->orderBy('name')
            ->get(['id', 'name', 'academic_id', 'email']);

        $navBadges = $this->getNavBadges($userId);

        return view('alumni.recommendations', [
            'received' => $received,
            'given' => $given,
            'alumniList' => $alumniList,
            'jobBadgeCount' => $navBadges['jobBadgeCount'],
            'workshopBadgeCount' => $navBadges['workshopBadgeCount'],
            'applicationsBadgeCount' => $navBadges['applicationsBadgeCount'],
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'role_title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $toUser = User::query()
            ->where('role', 'alumni')
            ->where('id', '!=', $user->id)
            ->findOrFail($data['to_user_id']);

        Recommendation::create([
            'from_user_id' => $user->id,
            'to_user_id' => $toUser->id,
            'from_name' => $user->name,
            'to_name' => $toUser->name,
            'role_title' => $data['role_title'],
            'content' => $data['content'],
            'date' => now()->format('M d, Y'),
        ]);

        return back()->with('toast_success', 'Recommendation sent successfully!');
    }

    public function destroy(Recommendation $recommendation)
    {
        if ((int) $recommendation->from_user_id !== (int) Auth::id()) {
            abort(403);
        }

        $recommendation->delete();

        return back()->with('toast_success', 'Recommendation deleted.');
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
        $scholarshipApplicationsCount = ScholarshipApplication::where('alumni_user_id', $userId)->count();
        $registeredWorkshopsCount = (clone $registeredWorkshopsQuery)->count();

        return [
            'jobBadgeCount' => (clone $jobsQuery)->count(),
            'workshopBadgeCount' => (clone $workshopsQuery)->count(),
            'applicationsBadgeCount' => $jobApplicationsCount + $scholarshipApplicationsCount + $registeredWorkshopsCount,
        ];
    }
}
