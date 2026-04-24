<?php

namespace App\Support;

use App\Models\Announcement;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\SuccessStory;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardNavigation
{
    public static function build(?User $user, int $overviewBadge = 0): array
    {
        if (!$user) {
            return [];
        }

        return match ($user->role) {
            'alumni' => self::forAlumni($user, $overviewBadge),
            'college' => self::forCollege($overviewBadge),
            'company' => self::forCompany($user, $overviewBadge),
            'admin', 'super_admin' => self::forAdmin($overviewBadge),
            default => [],
        };
    }

    private static function forAlumni(User $user, int $overviewBadge): array
    {
        $userId = (int) $user->id;

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

        $scholarshipsQuery = Scholarship::query();
        if (Schema::hasColumn('scholarships', 'status')) {
            $scholarshipsQuery->where('status', 'active');
        }

        $registeredWorkshopsQuery = WorkshopRegistration::where('alumni_user_id', $userId);
        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $registeredWorkshopsQuery->where('status', 'registered');
        }

        $jobApplicationsCount = JobApplication::where('alumni_user_id', $userId)->count();
        $scholarshipApplicationsCount = self::tableExists('scholarship_applications')
            ? ScholarshipApplication::where('alumni_user_id', $userId)->count()
            : 0;
        $registeredWorkshopsCount = (clone $registeredWorkshopsQuery)->count();

        return [
            ['label' => __('Overview'), 'href' => '/alumni', 'icon' => 'layout-dashboard', 'badge' => $overviewBadge],
            ['label' => __('My Profile'), 'href' => '/alumni/profile', 'icon' => 'user'],
            ['label' => __('Job Opportunities'), 'href' => '/alumni/jobs', 'icon' => 'briefcase', 'badge' => (clone $jobsQuery)->count()],
            ['label' => __('Workshops'), 'href' => '/alumni/workshops', 'icon' => 'calendar-days', 'badge' => (clone $workshopsQuery)->count()],
            ['label' => __('Scholarships'), 'href' => '/alumni/scholarships', 'icon' => 'graduation-cap', 'badge' => (clone $scholarshipsQuery)->count()],
            ['label' => __('Recommendations'), 'href' => '/alumni/recommendations', 'icon' => 'message-square', 'badge' => Recommendation::where('to_user_id', $userId)->count()],
            ['label' => __('Leaderboard'), 'href' => '/alumni/leaderboard', 'icon' => 'trophy'],
            ['label' => __('My Applications'), 'href' => '/alumni/applications', 'icon' => 'file-text', 'badge' => $jobApplicationsCount + $scholarshipApplicationsCount + $registeredWorkshopsCount],
        ];
    }

    private static function forCollege(int $overviewBadge): array
    {
        return [
            ['label' => 'Overview', 'href' => '/college', 'icon' => 'layout-dashboard', 'badge' => $overviewBadge],
            ['label' => 'Browse Alumni', 'href' => '/college/alumni', 'icon' => 'users', 'badge' => User::where('role', 'alumni')->count()],
            ['label' => 'Workshops', 'href' => '/college/workshops', 'icon' => 'calendar-days', 'badge' => self::tableExists('workshops') ? Workshop::count() : 0],
            ['label' => 'Job Postings', 'href' => '/college/jobs', 'icon' => 'briefcase', 'badge' => self::tableExists('jobs') ? Job::count() : 0],
            ['label' => 'Announcements', 'href' => '/college/announcements', 'icon' => 'megaphone', 'badge' => self::tableExists('announcements') ? Announcement::count() : 0],
            ['label' => 'Scholarships', 'href' => '/college/scholarships', 'icon' => 'graduation-cap', 'badge' => self::tableExists('scholarships') ? Scholarship::count() : 0],
            ['label' => 'Success Stories', 'href' => '/college/success-stories', 'icon' => 'award', 'badge' => self::tableExists('success_stories') ? SuccessStory::count() : 0],
            ['label' => 'Reports', 'href' => '/college/reports', 'icon' => 'bar-chart-3'],
        ];
    }

    private static function forCompany(User $user, int $overviewBadge): array
    {
        $companyId = (int) $user->id;

        $companyJobsQuery = Job::query()
            ->where('organizer_role', 'company')
            ->where('company_user_id', $companyId);

        $jobIds = (clone $companyJobsQuery)->pluck('id');

        return [
            ['label' => 'Overview', 'href' => '/company', 'icon' => 'layout-dashboard', 'badge' => $overviewBadge],
            ['label' => 'My Job Postings', 'href' => '/company/jobs', 'icon' => 'briefcase', 'badge' => (clone $companyJobsQuery)->count()],
            ['label' => 'Browse Alumni', 'href' => '/company/alumni', 'icon' => 'users', 'badge' => User::where('role', 'alumni')->count()],
            ['label' => 'Applications', 'href' => '/company/applications', 'icon' => 'file-text', 'badge' => JobApplication::whereIn('job_id', $jobIds)->count()],
            ['label' => 'Workshops', 'href' => '/company/workshops', 'icon' => 'calendar-days', 'badge' => self::tableExists('workshops') ? Workshop::where('company_user_id', $companyId)->count() : 0],
        ];
    }

    private static function forAdmin(int $overviewBadge): array
    {
        $pendingCompanies = self::tableExists('company_profiles')
            ? DB::table('company_profiles')->where('status', 'pending')->count()
            : 0;

        $openSupportTickets = self::tableExists('support_tickets')
            ? SupportTicket::whereIn('status', ['open', 'in_progress'])->count()
            : 0;

        $contentItems = 0;
        if (self::tableExists('announcements')) {
            $contentItems += Announcement::count();
        }
        if (self::tableExists('success_stories')) {
            $contentItems += SuccessStory::count();
        }
        if (self::tableExists('workshops')) {
            $contentItems += Workshop::count();
        }
        if (self::tableExists('scholarships')) {
            $contentItems += Scholarship::count();
        }

        return [
            ['label' => 'Overview', 'href' => '/admin', 'icon' => 'layout-dashboard', 'badge' => $overviewBadge],
            ['label' => 'User Management', 'href' => '/admin/users', 'icon' => 'users', 'badge' => User::count()],
            ['label' => 'Content Management', 'href' => '/admin/content', 'icon' => 'file-text', 'badge' => $contentItems],
            ['label' => 'Company Approvals', 'href' => '/admin/company-approvals', 'icon' => 'check-circle', 'badge' => $pendingCompanies],
            ['label' => 'Support Center', 'href' => '/admin/support', 'icon' => 'help-circle', 'badge' => $openSupportTickets],
            ['label' => 'Reports', 'href' => '/admin/reports', 'icon' => 'bar-chart-3'],
            ['label' => 'System Settings', 'href' => '/admin/settings', 'icon' => 'settings'],
        ];
    }

    private static function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
