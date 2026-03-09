<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $schema = DB::getSchemaBuilder();

        $totalUsers = DB::table('users')->count();

        $pendingApprovals = DB::table('company_profiles')
            ->where('status', 'pending')
            ->count();

        $jobsQuery = DB::table('jobs');
        if ($schema->hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }
        if ($schema->hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }
        $activeJobPosts = $jobsQuery->count();

        $monthlyLogins = DB::table('users')
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->startOfMonth())
            ->count();

        $newUsersThisMonth = DB::table('users')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $newJobsThisMonth = DB::table('jobs')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $pendingCompanies = DB::table('company_profiles')
            ->leftJoin('users', 'users.id', '=', 'company_profiles.user_id')
            ->select(
                'company_profiles.id',
                'company_profiles.company_name',
                'company_profiles.industry',
                'company_profiles.created_at',
                'users.email',
                'users.name'
            )
            ->where('company_profiles.status', 'pending')
            ->orderByDesc('company_profiles.id')
            ->limit(3)
            ->get();

        $recentActivity = [];

        $latestAlumni = DB::table('users')->where('role', 'alumni')->orderByDesc('id')->first();
        if ($latestAlumni) {
            $recentActivity[] = [
                'icon' => 'user-plus',
                'title' => 'New alumni registered',
                'time' => Carbon::parse($latestAlumni->created_at)->diffForHumans(),
            ];
        }

        $latestApprovedCompany = DB::table('company_profiles')
            ->where('status', 'approved')
            ->orderByDesc('approved_at')
            ->first();

        if ($latestApprovedCompany && !empty($latestApprovedCompany->approved_at)) {
            $recentActivity[] = [
                'icon' => 'building-2',
                'title' => 'Company approved',
                'time' => Carbon::parse($latestApprovedCompany->approved_at)->diffForHumans(),
            ];
        }

        $latestWorkshop = DB::table('workshops')->orderByDesc('id')->first();
        if ($latestWorkshop) {
            $recentActivity[] = [
                'icon' => 'calendar-days',
                'title' => 'Workshop created',
                'time' => Carbon::parse($latestWorkshop->created_at)->diffForHumans(),
            ];
        }

        $latestJob = DB::table('jobs')->orderByDesc('id')->first();
        if ($latestJob) {
            $recentActivity[] = [
                'icon' => 'briefcase',
                'title' => 'Job posted',
                'time' => Carbon::parse($latestJob->created_at)->diffForHumans(),
            ];
        }

        $systemSnapshot = [
            [
                'label' => 'Approved Companies',
                'value' => DB::table('company_profiles')->where('status', 'approved')->count(),
                'icon' => 'building-2',
            ],
            [
                'label' => 'Open Support Tickets',
                'value' => $schema->hasTable('support_tickets')
                    ? DB::table('support_tickets')->whereIn('status', ['open', 'in_progress'])->count()
                    : 0,
                'icon' => 'help-circle',
            ],
            [
                'label' => 'Workshops',
                'value' => DB::table('workshops')->count(),
                'icon' => 'calendar-days',
            ],
        ];

        $usersByRole = [
            ['role' => 'Alumni', 'count' => DB::table('users')->where('role', 'alumni')->count()],
            ['role' => 'College', 'count' => DB::table('users')->where('role', 'college')->count()],
            ['role' => 'Companies', 'count' => DB::table('users')->where('role', 'company')->count()],
            ['role' => 'Admins', 'count' => DB::table('users')->whereIn('role', ['admin','super_admin'])->count()],
        ];

        return view('admin.index', compact(
            'totalUsers',
            'pendingApprovals',
            'activeJobPosts',
            'monthlyLogins',
            'newUsersThisMonth',
            'newJobsThisMonth',
            'pendingCompanies',
            'recentActivity',
            'systemSnapshot',
            'usersByRole'
        ));
    }
}
