<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Workshop;
use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\WorkshopRegistration;
use App\Models\ScholarshipApplication;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {

        $totalUsers = User::count();

        $activeJobsQuery = Job::query();
        if (Schema::hasColumn('jobs', 'status')) {
            $activeJobsQuery->where('status', 'active');
        }
        if (Schema::hasColumn('jobs', 'approval_status')) {
            $activeJobsQuery->where('approval_status', 'approved');
        }
        $activeJobs = $activeJobsQuery->count();

        $workshopsQuery = Workshop::query();

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshopsQuery->where('proposal_status', 'approved');
        }

        $workshops = $workshopsQuery->whereYear('created_at', now()->year)->count();

        $companiesQuery = User::where('role', 'company');

        if (Schema::hasTable('company_profiles') && Schema::hasColumn('company_profiles', 'status')) {
            $companiesQuery->whereIn('id', function ($q) {
                $q->select('user_id')->from('company_profiles')->where('status', 'approved');
            });
        }
        $companies = $companiesQuery->count();


        $start = now()->subMonths(5)->startOfMonth();
        $raw = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->pluck('c', 'ym');

        $growth = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i)->startOfMonth();
            $key = $m->format('Y-m');
            $growth[] = [
                'label' => $m->format('F'),
                'value' => (int)($raw[$key] ?? 0),
            ];
        }


        $activity = [
            'Job Applications' => Schema::hasTable('job_applications') ? JobApplication::count() : 0,
            'Workshop Registrations' => Schema::hasTable('workshop_registrations')
                ? WorkshopRegistration::when(Schema::hasColumn('workshop_registrations', 'status'), fn($q) => $q->where('status', 'registered'))->count()
                : 0,
            'Profile Updates' => Schema::hasTable('alumni_profiles')
                ? DB::table('alumni_profiles')->where('updated_at', '>=', now()->subDays(30))->count()
                : 0,
            'Recommendations Given' => Schema::hasTable('recommendations') ? Recommendation::count() : 0,
            'Scholarship Applications' => Schema::hasTable('scholarship_applications') ? ScholarshipApplication::count() : 0,
        ];

        return view('admin.reports', compact(
            'totalUsers',
            'activeJobs',
            'workshops',
            'companies',
            'growth',
            'activity'
        ));
    }


    public function exportExcel()
    {
        $filename = 'admin_reports_' . now()->format('Y-m-d_H-i') . '.csv';

        
        $totalUsers = User::count();

        $activeJobsQuery = Job::query();
        if (Schema::hasColumn('jobs', 'status')) $activeJobsQuery->where('status', 'active');
        if (Schema::hasColumn('jobs', 'approval_status')) $activeJobsQuery->where('approval_status', 'approved');
        $activeJobs = $activeJobsQuery->count();

        $workshopsQuery = Workshop::query();
        if (Schema::hasColumn('workshops', 'proposal_status')) $workshopsQuery->where('proposal_status', 'approved');
        $workshops = $workshopsQuery->whereYear('created_at', now()->year)->count();

        $companiesQuery = User::where('role', 'company');
        if (Schema::hasTable('company_profiles') && Schema::hasColumn('company_profiles', 'status')) {
            $companiesQuery->whereIn('id', function ($q) {
                $q->select('user_id')->from('company_profiles')->where('status', 'approved');
            });
        }
        $companies = $companiesQuery->count();

        $rows = [
            ['Metric', 'Value'],
            ['Total Users', $totalUsers],
            ['Active Jobs', $activeJobs],
            ['Workshops (This Year)', $workshops],
            ['Approved Companies', $companies],
        ];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
