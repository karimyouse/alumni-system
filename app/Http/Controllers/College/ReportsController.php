<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\Scholarship;
use App\Models\SuccessStory;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportsController extends Controller
{
    public function index()
    {
        $totalAlumni = User::where('role', 'alumni')->count();
        $partnerCompanies = User::where('role', 'company')->count();
        $workshopsHeld = Workshop::count();
        $jobsCount = Job::count();

        $employedAlumni = 0;
        $employmentRate = 0;

        if (Schema::hasTable('alumni_profiles') && Schema::hasColumn('alumni_profiles', 'employment_status')) {
            $employedAlumni = DB::table('alumni_profiles')
                ->where('employment_status', 'employed')
                ->count();

            $employmentRate = $totalAlumni > 0
                ? (int) round(($employedAlumni / $totalAlumni) * 100)
                : 0;
        }

        $industryData = [];
        if (Schema::hasTable('alumni_profiles') && Schema::hasColumn('alumni_profiles', 'employment_industry')) {
            $industryRows = DB::table('alumni_profiles')
                ->select('employment_industry', DB::raw('COUNT(*) as total'))
                ->whereNotNull('employment_industry')
                ->where('employment_industry', '!=', '')
                ->groupBy('employment_industry')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $industryTotal = $industryRows->sum('total');

            $industryData = $industryRows->map(function ($row) use ($industryTotal) {
                $percent = $industryTotal > 0 ? (int) round(($row->total / $industryTotal) * 100) : 0;

                return [
                    'name' => $row->employment_industry,
                    'count' => (int) $row->total,
                    'percent' => $percent,
                ];
            })->values()->all();
        }

        if (empty($industryData)) {
            $industryData = [
                ['name' => 'No industry data yet', 'count' => 0, 'percent' => 0],
            ];
        }

        $graduationYearData = [];
        if (Schema::hasTable('alumni_profiles') && Schema::hasColumn('alumni_profiles', 'graduation_year')) {
            $yearRows = DB::table('alumni_profiles')
                ->select('graduation_year', DB::raw('COUNT(*) as total'))
                ->whereNotNull('graduation_year')
                ->where('graduation_year', '!=', '')
                ->groupBy('graduation_year')
                ->orderByDesc('graduation_year')
                ->limit(6)
                ->get();

            $maxYearCount = (int) ($yearRows->max('total') ?? 0);

            $graduationYearData = $yearRows->map(function ($row) use ($maxYearCount) {
                $percent = $maxYearCount > 0 ? (int) round(($row->total / $maxYearCount) * 100) : 0;

                return [
                    'year' => (string) $row->graduation_year,
                    'count' => (int) $row->total,
                    'percent' => $percent,
                ];
            })->values()->all();
        }

        if (empty($graduationYearData)) {
            $graduationYearData = [
                ['year' => 'N/A', 'count' => 0, 'percent' => 0],
            ];
        }

        $announcementsPublished = Announcement::where('is_published', true)->count();
        $announcementsTotal = Announcement::count();

        $storiesPublished = SuccessStory::where('is_published', true)->count();
        $storiesTotal = SuccessStory::count();

        $scholarshipsTotal = Scholarship::count();

        $collegeUsers = User::where('role', 'college')->count();
        $admins = User::whereIn('role', ['admin', 'super_admin'])->count();

        return view('college.reports', [
            'totalAlumni' => $totalAlumni,
            'employmentRate' => $employmentRate,
            'partnerCompanies' => $partnerCompanies,
            'workshopsHeld' => $workshopsHeld,

            'industryData' => $industryData,
            'graduationYearData' => $graduationYearData,

            'announcementsPublished' => $announcementsPublished,
            'announcementsTotal' => $announcementsTotal,
            'storiesPublished' => $storiesPublished,
            'storiesTotal' => $storiesTotal,
            'scholarshipsTotal' => $scholarshipsTotal,

            'collegeUsers' => $collegeUsers,
            'admins' => $admins,
            'jobsCount' => $jobsCount,
        ]);
    }
}
