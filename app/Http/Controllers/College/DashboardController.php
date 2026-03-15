<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\Scholarship;
use App\Models\SuccessStory;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlumni = User::where('role', 'alumni')->count();

        $employmentRate = 0;
        if (Schema::hasTable('alumni_profiles') && Schema::hasColumn('alumni_profiles', 'employment_status')) {
            $employed = AlumniProfile::where('employment_status', 'Employed')->count();
            $employmentRate = $totalAlumni > 0 ? (int) round(($employed / $totalAlumni) * 100) : 0;
        }

        $jobsQuery = Job::query();
        if (Schema::hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }
        if (Schema::hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }
        $activeJobPosts = Schema::hasTable('jobs') ? $jobsQuery->count() : 0;

        $workshopsQuery = Workshop::query();
        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshopsQuery->where('proposal_status', 'approved');
        }
        if (Schema::hasColumn('workshops', 'status')) {
            $workshopsQuery->where('status', 'upcoming');
        }
        $upcomingCount = Schema::hasTable('workshops') ? (clone $workshopsQuery)->count() : 0;

        $recentAlumni = User::where('role', 'alumni')
            ->with('alumniProfile')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $upcomingEventsQuery = Workshop::query()->orderByDesc('id')->take(3);

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $upcomingEventsQuery->where('proposal_status', 'approved');
        }
        if (Schema::hasColumn('workshops', 'status')) {
            $upcomingEventsQuery->where('status', 'upcoming');
        }

        if (method_exists(Workshop::class, 'registrations')) {
            $upcomingEventsQuery->withCount([
                'registrations as registered_count' => function ($q) {
                    if (Schema::hasColumn('workshop_registrations', 'status')) {
                        $q->where('status', 'registered');
                    }
                }
            ]);
        }

        $upcomingEvents = $upcomingEventsQuery->get();

        $departmentStats = $this->buildDepartmentStats();

        return view('college.index', array_merge([
            'totalAlumni' => $totalAlumni,
            'employmentRate' => $employmentRate,
            'activeJobPosts' => $activeJobPosts,
            'upcomingCount' => $upcomingCount,
            'recentAlumni' => $recentAlumni,
            'upcomingEvents' => $upcomingEvents,
            'departmentStats' => $departmentStats,
        ], $this->buildNavCounts()));
    }

    private function buildDepartmentStats(): array
    {
        if (!Schema::hasTable('alumni_profiles') || !Schema::hasColumn('alumni_profiles', 'major')) {
            return [];
        }

        $rows = AlumniProfile::query()
            ->selectRaw('major, COUNT(*) as alumni_count')
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->groupBy('major')
            ->orderByDesc('alumni_count')
            ->limit(4)
            ->get();

        return $rows->map(function ($row) {
            $major = (string) $row->major;
            $alumniCount = (int) $row->alumni_count;

            $employedPercent = 0;
            if (Schema::hasColumn('alumni_profiles', 'employment_status')) {
                $employedCount = AlumniProfile::where('major', $major)
                    ->where('employment_status', 'Employed')
                    ->count();

                $employedPercent = $alumniCount > 0
                    ? (int) round(($employedCount / $alumniCount) * 100)
                    : 0;
            }

            return [
                'name' => $major,
                'alumni' => $alumniCount,
                'employed' => $employedPercent,
            ];
        })->values()->all();
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
