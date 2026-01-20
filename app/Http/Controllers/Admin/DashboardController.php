<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Models\Job;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingCompaniesCount = CompanyProfile::where('status', 'pending')->count();
        $totalCompaniesCount   = CompanyProfile::count();
        $totalUsersCount       = User::count();
        $totalJobsCount        = class_exists(Job::class) ? Job::count() : 0;

        $recentNotifications = AdminNotification::orderByDesc('id')->take(6)->get();
        $unreadNotificationsCount = AdminNotification::where('is_read', false)->count();

        $pendingCompanies = CompanyProfile::with('user')
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'pendingCompaniesCount',
            'totalCompaniesCount',
            'totalUsersCount',
            'totalJobsCount',
            'recentNotifications',
            'unreadNotificationsCount',
            'pendingCompanies'
        ));
    }
}
