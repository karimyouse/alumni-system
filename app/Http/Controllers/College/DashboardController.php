<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Workshop;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlumni = User::where('role', 'alumni')->count();

        // ✅ employmentRate: إذا عندك alumni_profiles.employment_status
        $employmentRate = 0;
        if (Schema::hasTable('alumni_profiles') && Schema::hasColumn('alumni_profiles', 'employment_status')) {
            $employed = \App\Models\AlumniProfile::where('employment_status', 'Employed')->count();
            $employmentRate = $totalAlumni > 0 ? (int) round(($employed / $totalAlumni) * 100) : 0;
        }

        // ✅ jobs count
        $activeJobPosts = 0;
        if (class_exists(Job::class) && Schema::hasTable('jobs')) {
            if (Schema::hasColumn('jobs', 'status')) {
                $activeJobPosts = Job::where('status', 'active')->count();
            } else {
                $activeJobPosts = Job::count();
            }
        }

        // ✅ upcoming workshops count
        $upcomingCount = Workshop::count();

        // ✅ recent alumni (latest 4)
        $recentAlumni = User::where('role', 'alumni')
            ->with('alumniProfile')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        // ✅ upcoming events (latest 3 workshops) + registered_count
        $upcomingEventsQuery = Workshop::query()->orderByDesc('id')->take(3);

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

        // ✅ departmentStats fallback مؤقت
        $departmentStats = [
            ['name'=>'Computer Science','alumni'=>0,'employed'=>0],
            ['name'=>'Information Technology','alumni'=>0,'employed'=>0],
            ['name'=>'Web Development','alumni'=>0,'employed'=>0],
            ['name'=>'Networking','alumni'=>0,'employed'=>0],
        ];

        return view('college.index', compact(
            'totalAlumni',
            'employmentRate',
            'activeJobPosts',
            'upcomingCount',
            'recentAlumni',
            'upcomingEvents',
            'departmentStats'
        ));
    }
}
