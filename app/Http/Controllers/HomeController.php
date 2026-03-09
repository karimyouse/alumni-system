<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\Job;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'alumni' => 0,
            'jobs' => 0,
            'workshops' => 0,
            'companies' => 0,
        ];

        try {
            if (Schema::hasTable('users')) {
                $alumniQuery = User::query()->where('role', 'alumni');

                if (Schema::hasColumn('users', 'is_suspended')) {
                    $alumniQuery->where(function ($q) {
                        $q->whereNull('is_suspended')->orWhere('is_suspended', false);
                    });
                }

                $stats['alumni'] = $alumniQuery->count();
            }

            if (Schema::hasTable('jobs')) {
                $jobsQuery = Job::query();

                if (Schema::hasColumn('jobs', 'approval_status')) {
                    $jobsQuery->where('approval_status', 'approved');
                } elseif (Schema::hasColumn('jobs', 'status')) {
                    $jobsQuery->where('status', 'active');
                }

                $stats['jobs'] = $jobsQuery->count();
            }

            if (Schema::hasTable('workshops')) {
                $workshopsQuery = Workshop::query();

                if (Schema::hasColumn('workshops', 'proposal_status')) {
                    $workshopsQuery->where('proposal_status', 'approved');
                }

                $stats['workshops'] = $workshopsQuery->count();
            }

            if (Schema::hasTable('company_profiles')) {
                $companiesQuery = CompanyProfile::query();

                if (Schema::hasColumn('company_profiles', 'status')) {
                    $companiesQuery->where('status', 'approved');
                }

                $stats['companies'] = $companiesQuery->count();
            }
        } catch (\Throwable $e) {
            // keep safe fallback zeros
        }

        return view('home', [
            'homeStats' => $stats,
        ]);
    }
}
