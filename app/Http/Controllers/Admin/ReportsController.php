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
        $data = $this->dashboardData();

        return view('admin.reports', $data);
    }


    public function exportPdf()
    {
        return view('reports.print', [
            'report' => $this->reportPayload(),
        ]);
    }


    public function exportExcel()
    {
        $report = $this->reportPayload();
        $filename = 'admin_reports_' . now()->format('Y-m-d_H-i') . '.xls';
        $html = view('reports.excel', compact('report'))->render();

        return response("\xEF\xBB\xBF" . $html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }


    private function dashboardData(): array
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

        return compact(
            'totalUsers',
            'activeJobs',
            'workshops',
            'companies',
            'growth',
            'activity'
        );
    }


    private function reportPayload(): array
    {
        $data = $this->dashboardData();
        $maxGrowth = max(collect($data['growth'])->max('value') ?: 1, 1);
        $maxActivity = max(collect($data['activity'])->max() ?: 1, 1);
        $newUsers30 = User::where('created_at', '>=', now()->subDays(30))->count();
        $suspendedUsers = Schema::hasColumn('users', 'is_suspended')
            ? User::where('is_suspended', true)->count()
            : 0;

        return [
            'title' => 'Admin Analytics Report',
            'subtitle' => 'System-wide performance, governance, growth, and engagement overview',
            'accent' => '#d97706',
            'period' => now()->subMonths(5)->startOfMonth()->format('M Y') . ' - ' . now()->format('M Y'),
            'generated_at' => now()->format('Y-m-d H:i'),
            'generated_by' => auth()->user()->name ?? 'System',
            'insights' => [
                ['label' => 'New Users in Last 30 Days', 'value' => number_format($newUsers30)],
                ['label' => 'Suspended Accounts', 'value' => number_format($suspendedUsers)],
                ['label' => 'Tracked Activity Records', 'value' => number_format(array_sum($data['activity']))],
                ['label' => 'Report Coverage', 'value' => 'Users, jobs, workshops, companies, content'],
            ],
            'cards' => [
                ['label' => 'Total Users', 'value' => number_format($data['totalUsers']), 'note' => 'All registered users'],
                ['label' => 'Active Jobs', 'value' => number_format($data['activeJobs']), 'note' => 'Approved open positions'],
                ['label' => 'Workshops', 'value' => number_format($data['workshops']), 'note' => 'Approved this year'],
                ['label' => 'Companies', 'value' => number_format($data['companies']), 'note' => 'Approved partners'],
            ],
            'sections' => [
                [
                    'title' => 'User Growth by Month',
                    'columns' => ['Month', 'New Users', 'Share'],
                    'rows' => collect($data['growth'])->map(fn($item) => [
                        $item['label'],
                        number_format($item['value']),
                        (int) round(($item['value'] / $maxGrowth) * 100) . '%',
                    ])->all(),
                ],
                [
                    'title' => 'Activity Summary',
                    'columns' => ['Activity', 'Total', 'Relative Weight'],
                    'rows' => collect($data['activity'])->map(fn($value, $label) => [
                        $label,
                        number_format($value),
                        (int) round(($value / $maxActivity) * 100) . '%',
                    ])->values()->all(),
                ],
                [
                    'title' => 'User Role Distribution',
                    'description' => 'Breakdown of platform accounts by role with each role share from all users.',
                    'columns' => ['Role', 'Total Users', 'Share'],
                    'rows' => $this->userRoleRows($data['totalUsers']),
                ],
                [
                    'title' => 'Governance and Approval Pipeline',
                    'description' => 'Operational status for company approvals, job moderation, and account controls.',
                    'columns' => ['Area', 'Count', 'Share or Status'],
                    'rows' => array_merge(
                        $this->companyApprovalRows(),
                        $this->jobPipelineRows(),
                        [['Suspended accounts', number_format($suspendedUsers), $data['totalUsers'] > 0 ? $this->percent($suspendedUsers, $data['totalUsers']) : '0%']]
                    ),
                ],
                [
                    'title' => 'Application and Engagement Breakdown',
                    'description' => 'Detailed totals and status summaries for platform actions.',
                    'columns' => ['Engagement Type', 'Total', 'Status Breakdown'],
                    'rows' => $this->engagementRows(),
                ],
                [
                    'title' => 'Top Jobs by Applications',
                    'description' => 'Highest-demand job postings with visibility and moderation state.',
                    'columns' => ['Job Title', 'Company', 'Location', 'Status', 'Applications', 'Views'],
                    'rows' => $this->topJobsRows(),
                ],
                [
                    'title' => 'Workshop Schedule and Capacity',
                    'description' => 'Recent and upcoming workshop records with registration load.',
                    'columns' => ['Workshop', 'Date', 'Location', 'Status', 'Registrations', 'Capacity'],
                    'rows' => $this->workshopRows(),
                ],
                [
                    'title' => 'Content Publishing Summary',
                    'description' => 'Published and draft content counts across college-facing content areas.',
                    'columns' => ['Content Type', 'Published or Open', 'Total', 'Share'],
                    'rows' => $this->contentRows(),
                ],
                [
                    'title' => 'Recently Registered Users',
                    'description' => 'Latest user accounts created in the system for quick audit review.',
                    'columns' => ['Name', 'Email', 'Role', 'Registered At'],
                    'rows' => $this->recentUsersRows(),
                ],
            ],
        ];
    }

    private function userRoleRows(int $totalUsers): array
    {
        return User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                $this->label($row->role ?: 'unassigned'),
                number_format($row->total),
                $this->percent((int) $row->total, $totalUsers),
            ])
            ->values()
            ->all();
    }

    private function companyApprovalRows(): array
    {
        if (!Schema::hasTable('company_profiles') || !Schema::hasColumn('company_profiles', 'status')) {
            return [['Company approval data', '-', 'Not available']];
        }

        $total = DB::table('company_profiles')->count();

        return DB::table('company_profiles')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'Companies: ' . $this->label($row->status),
                number_format($row->total),
                $this->percent((int) $row->total, $total),
            ])
            ->values()
            ->all();
    }

    private function jobPipelineRows(): array
    {
        if (!Schema::hasTable('jobs')) {
            return [['Job moderation data', '-', 'Not available']];
        }

        $rows = [];
        $total = DB::table('jobs')->count();

        foreach (['approval_status' => 'Job approvals', 'status' => 'Job status'] as $column => $label) {
            if (!Schema::hasColumn('jobs', $column)) {
                continue;
            }

            DB::table('jobs')
                ->select($column, DB::raw('COUNT(*) as total'))
                ->groupBy($column)
                ->orderByDesc('total')
                ->get()
                ->each(function ($row) use (&$rows, $column, $label, $total) {
                    $rows[] = [
                        $label . ': ' . $this->label($row->{$column} ?: 'unknown'),
                        number_format($row->total),
                        $this->percent((int) $row->total, $total),
                    ];
                });
        }

        return $rows ?: [['Job moderation data', number_format($total), 'No status columns']];
    }

    private function engagementRows(): array
    {
        $workshopRegistrations = 0;
        if (Schema::hasTable('workshop_registrations')) {
            $workshopRegistrationsQuery = DB::table('workshop_registrations');
            if (Schema::hasColumn('workshop_registrations', 'status')) {
                $workshopRegistrationsQuery->where('status', 'registered');
            }
            $workshopRegistrations = $workshopRegistrationsQuery->count();
        }

        return [
            ['Job applications', number_format($this->tableCount('job_applications')), $this->statusSummary('job_applications')],
            ['Workshop registrations', number_format($workshopRegistrations), $this->statusSummary('workshop_registrations')],
            ['Scholarship applications', number_format($this->tableCount('scholarship_applications')), $this->statusSummary('scholarship_applications')],
            ['Recommendations', number_format($this->tableCount('recommendations')), 'Total recommendations submitted'],
            ['Profile updates in last 30 days', number_format(Schema::hasTable('alumni_profiles') ? DB::table('alumni_profiles')->where('updated_at', '>=', now()->subDays(30))->count() : 0), 'Recently refreshed alumni profiles'],
        ];
    }

    private function topJobsRows(): array
    {
        if (!Schema::hasTable('jobs')) {
            return [['No job data available', '-', '-', '-', '-', '-']];
        }

        $query = DB::table('jobs')
            ->select([
                'jobs.id',
                'jobs.title',
                'jobs.company_name',
                'jobs.location',
                'jobs.status',
                DB::raw((Schema::hasColumn('jobs', 'approval_status') ? 'jobs.approval_status' : "''") . ' as approval_status'),
                DB::raw((Schema::hasColumn('jobs', 'views') ? 'jobs.views' : '0') . ' as views'),
            ]);

        if (Schema::hasTable('job_applications')) {
            $groupBy = ['jobs.id', 'jobs.title', 'jobs.company_name', 'jobs.location', 'jobs.status'];
            if (Schema::hasColumn('jobs', 'approval_status')) {
                $groupBy[] = 'jobs.approval_status';
            }
            if (Schema::hasColumn('jobs', 'views')) {
                $groupBy[] = 'jobs.views';
            }

            $query->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
                ->addSelect(DB::raw('COUNT(job_applications.id) as applications'))
                ->groupBy(...$groupBy)
                ->orderByDesc('applications');
        } else {
            $query->addSelect(DB::raw('0 as applications'))->orderByDesc('jobs.created_at');
        }

        return $query->limit(8)->get()->map(fn($job) => [
            $job->title ?: '-',
            $job->company_name ?: '-',
            $job->location ?: '-',
            trim($this->label($job->status) . ' / ' . $this->label($job->approval_status ?: 'approved'), ' /'),
            number_format($job->applications),
            number_format($job->views),
        ])->values()->all() ?: [['No job data available', '-', '-', '-', '-', '-']];
    }

    private function workshopRows(): array
    {
        if (!Schema::hasTable('workshops')) {
            return [['No workshop data available', '-', '-', '-', '-', '-']];
        }

        $query = DB::table('workshops')
            ->select([
                'workshops.id',
                'workshops.title',
                'workshops.date',
                'workshops.location',
                'workshops.status',
                DB::raw((Schema::hasColumn('workshops', 'capacity') ? 'workshops.capacity' : (Schema::hasColumn('workshops', 'max_spots') ? 'workshops.max_spots' : '0')) . ' as capacity'),
            ]);

        if (Schema::hasTable('workshop_registrations')) {
            $groupBy = ['workshops.id', 'workshops.title', 'workshops.date', 'workshops.location', 'workshops.status'];
            if (Schema::hasColumn('workshops', 'capacity')) {
                $groupBy[] = 'workshops.capacity';
            } elseif (Schema::hasColumn('workshops', 'max_spots')) {
                $groupBy[] = 'workshops.max_spots';
            }

            $query->leftJoin('workshop_registrations', 'workshop_registrations.workshop_id', '=', 'workshops.id')
                ->when(
                    Schema::hasColumn('workshop_registrations', 'status'),
                    fn ($q) => $q->where('workshop_registrations.status', 'registered')
                )
                ->addSelect(DB::raw('COUNT(workshop_registrations.id) as registrations'))
                ->groupBy(...$groupBy)
                ->orderByDesc('registrations');
        } else {
            $query->addSelect(DB::raw('0 as registrations'))->orderByDesc('workshops.created_at');
        }

        return $query->limit(8)->get()->map(fn($workshop) => [
            $workshop->title ?: '-',
            $workshop->date ?: '-',
            $workshop->location ?: '-',
            $this->label($workshop->status ?: 'upcoming'),
            number_format($workshop->registrations),
            number_format($workshop->capacity),
        ])->values()->all() ?: [['No workshop data available', '-', '-', '-', '-', '-']];
    }

    private function contentRows(): array
    {
        $announcementsTotal = Schema::hasTable('announcements') ? DB::table('announcements')->count() : 0;
        $announcementsPublished = Schema::hasTable('announcements') && Schema::hasColumn('announcements', 'is_published')
            ? DB::table('announcements')->where('is_published', true)->count()
            : $announcementsTotal;
        $storiesTotal = Schema::hasTable('success_stories') ? DB::table('success_stories')->count() : 0;
        $storiesPublished = Schema::hasTable('success_stories') && Schema::hasColumn('success_stories', 'is_published')
            ? DB::table('success_stories')->where('is_published', true)->count()
            : $storiesTotal;
        $scholarshipsTotal = Schema::hasTable('scholarships') ? DB::table('scholarships')->count() : 0;
        $scholarshipsOpen = Schema::hasTable('scholarships') && Schema::hasColumn('scholarships', 'status')
            ? DB::table('scholarships')->where('status', 'active')->count()
            : $scholarshipsTotal;

        return [
            ['Announcements', number_format($announcementsPublished), number_format($announcementsTotal), $this->percent($announcementsPublished, $announcementsTotal)],
            ['Success stories', number_format($storiesPublished), number_format($storiesTotal), $this->percent($storiesPublished, $storiesTotal)],
            ['Scholarships', number_format($scholarshipsOpen), number_format($scholarshipsTotal), $this->percent($scholarshipsOpen, $scholarshipsTotal)],
        ];
    }

    private function recentUsersRows(): array
    {
        return User::select('name', 'email', 'role', 'created_at')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($user) => [
                $user->name ?: '-',
                $user->email ?: '-',
                $this->label($user->role ?: 'unassigned'),
                optional($user->created_at)->format('Y-m-d H:i') ?: '-',
            ])
            ->values()
            ->all();
    }

    private function statusSummary(string $table): string
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'status')) {
            return 'No status column';
        }

        $summary = DB::table($table)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => $this->label($row->status ?: 'unknown') . ': ' . number_format($row->total))
            ->implode(', ');

        return $summary !== '' ? $summary : 'No records';
    }

    private function tableCount(string $table): int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }

    private function percent(int $value, int $total): string
    {
        return $total > 0 ? (int) round(($value / $total) * 100) . '%' : '0%';
    }

    private function label(?string $value): string
    {
        return ucwords(str_replace('_', ' ', (string) $value));
    }
}
