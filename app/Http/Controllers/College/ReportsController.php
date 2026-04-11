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
        return view('college.reports', array_merge(
            $this->dashboardData(),
            $this->buildNavCounts()
        ));
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
        $filename = 'college_reports_' . now()->format('Y-m-d_H-i') . '.xls';
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

        return [
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
        ];
    }

    private function reportPayload(): array
    {
        $data = $this->dashboardData();
        $profilesTotal = Schema::hasTable('alumni_profiles')
            ? DB::table('alumni_profiles')->count()
            : 0;
        $profileCompletion = $data['totalAlumni'] > 0
            ? (int) round(($profilesTotal / $data['totalAlumni']) * 100)
            : 0;

        return [
            'title' => 'College Analytics Report',
            'subtitle' => 'Alumni outcomes, academic distribution, opportunities, and content performance overview',
            'accent' => '#16a34a',
            'period' => now()->subMonths(5)->startOfMonth()->format('M Y') . ' - ' . now()->format('M Y'),
            'generated_at' => now()->format('Y-m-d H:i'),
            'generated_by' => auth()->user()->name ?? 'System',
            'insights' => [
                ['label' => 'Profile Completion Coverage', 'value' => $profileCompletion . '%'],
                ['label' => 'Alumni Profiles Recorded', 'value' => number_format($profilesTotal)],
                ['label' => 'Open Scholarships', 'value' => number_format($this->openScholarshipsCount())],
                ['label' => 'Tracked Applications', 'value' => number_format($this->tableCount('job_applications') + $this->tableCount('scholarship_applications'))],
            ],
            'cards' => [
                ['label' => 'Total Alumni', 'value' => number_format($data['totalAlumni']), 'note' => 'Registered graduates'],
                ['label' => 'Employment Rate', 'value' => $data['employmentRate'] . '%', 'note' => 'Employed alumni'],
                ['label' => 'Partner Companies', 'value' => number_format($data['partnerCompanies']), 'note' => 'Registered companies'],
                ['label' => 'Workshops Held', 'value' => number_format($data['workshopsHeld']), 'note' => 'Total workshops'],
            ],
            'sections' => [
                [
                    'title' => 'Employment by Industry',
                    'columns' => ['Industry', 'Alumni', 'Share'],
                    'rows' => collect($data['industryData'])->map(fn($item) => [
                        $item['name'],
                        number_format($item['count']),
                        $item['percent'] . '%',
                    ])->all(),
                ],
                [
                    'title' => 'Graduation Year Distribution',
                    'columns' => ['Graduation Year', 'Alumni', 'Relative Share'],
                    'rows' => collect($data['graduationYearData'])->map(fn($item) => [
                        $item['year'],
                        number_format($item['count']),
                        $item['percent'] . '%',
                    ])->all(),
                ],
                [
                    'title' => 'Major Distribution',
                    'description' => 'Academic concentration of alumni profiles by major.',
                    'columns' => ['Major', 'Alumni', 'Share'],
                    'rows' => $this->majorRows($profilesTotal),
                ],
                [
                    'title' => 'Employment Status Breakdown',
                    'description' => 'Alumni employment status as captured in alumni profiles.',
                    'columns' => ['Employment Status', 'Alumni', 'Share'],
                    'rows' => $this->employmentRows($profilesTotal),
                ],
                [
                    'title' => 'Content Summary',
                    'description' => 'Publishing readiness and total records across college content modules.',
                    'columns' => ['Content Type', 'Published', 'Total'],
                    'rows' => [
                        ['Announcements', number_format($data['announcementsPublished']), number_format($data['announcementsTotal'])],
                        ['Success Stories', number_format($data['storiesPublished']), number_format($data['storiesTotal'])],
                        ['Scholarships', '-', number_format($data['scholarshipsTotal'])],
                        ['Jobs', '-', number_format($data['jobsCount'])],
                    ],
                ],
                [
                    'title' => 'Users Summary',
                    'description' => 'Role-level account totals relevant to the college dashboard.',
                    'columns' => ['Role', 'Total', 'Notes'],
                    'rows' => [
                        ['College Users', number_format($data['collegeUsers']), 'Internal users'],
                        ['Admins', number_format($data['admins']), 'Admin and super admin'],
                        ['Companies', number_format($data['partnerCompanies']), 'Company accounts'],
                        ['Alumni', number_format($data['totalAlumni']), 'Graduate accounts'],
                    ],
                ],
                [
                    'title' => 'Opportunity Demand: Jobs',
                    'description' => 'Job postings ranked by applicant demand with publishing status.',
                    'columns' => ['Job Title', 'Company', 'Type', 'Status', 'Applications', 'Posted'],
                    'rows' => $this->jobDemandRows(),
                ],
                [
                    'title' => 'Workshop Registration Detail',
                    'description' => 'Workshop capacity and registration pressure for planning decisions.',
                    'columns' => ['Workshop', 'Date', 'Location', 'Status', 'Registrations', 'Capacity'],
                    'rows' => $this->workshopDetailRows(),
                ],
                [
                    'title' => 'Scholarship Pipeline',
                    'description' => 'Scholarship opportunities with application volume and deadlines.',
                    'columns' => ['Scholarship', 'Amount', 'Deadline', 'Status', 'Applications'],
                    'rows' => $this->scholarshipRows(),
                ],
                [
                    'title' => 'Partner Company Industries',
                    'description' => 'Company profile distribution by industry for partnership visibility.',
                    'columns' => ['Industry', 'Companies', 'Share'],
                    'rows' => $this->companyIndustryRows(),
                ],
                [
                    'title' => 'Recently Registered Alumni',
                    'description' => 'Latest alumni accounts with available academic profile information.',
                    'columns' => ['Name', 'Email', 'Major', 'Graduation Year', 'Registered At'],
                    'rows' => $this->recentAlumniRows(),
                ],
            ],
        ];
    }

    private function majorRows(int $profilesTotal): array
    {
        if (!Schema::hasTable('alumni_profiles') || !Schema::hasColumn('alumni_profiles', 'major')) {
            return [['Major data', '-', 'Not available']];
        }

        $rows = DB::table('alumni_profiles')
            ->select('major', DB::raw('COUNT(*) as total'))
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->groupBy('major')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                $row->major,
                number_format($row->total),
                $this->percent((int) $row->total, $profilesTotal),
            ])
            ->values()
            ->all();

        return $rows ?: [['No major data yet', '0', '0%']];
    }

    private function employmentRows(int $profilesTotal): array
    {
        if (!Schema::hasTable('alumni_profiles') || !Schema::hasColumn('alumni_profiles', 'employment_status')) {
            return [['Employment data', '-', 'Not available']];
        }

        $rows = DB::table('alumni_profiles')
            ->select('employment_status', DB::raw('COUNT(*) as total'))
            ->whereNotNull('employment_status')
            ->where('employment_status', '!=', '')
            ->groupBy('employment_status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                $this->label($row->employment_status),
                number_format($row->total),
                $this->percent((int) $row->total, $profilesTotal),
            ])
            ->values()
            ->all();

        return $rows ?: [['No employment data yet', '0', '0%']];
    }

    private function jobDemandRows(): array
    {
        if (!Schema::hasTable('jobs')) {
            return [['No job data available', '-', '-', '-', '-', '-']];
        }

        $query = DB::table('jobs')
            ->select([
                'jobs.id',
                'jobs.title',
                'jobs.company_name',
                DB::raw((Schema::hasColumn('jobs', 'type') ? 'jobs.type' : "''") . ' as type'),
                DB::raw((Schema::hasColumn('jobs', 'status') ? 'jobs.status' : "''") . ' as status'),
                DB::raw((Schema::hasColumn('jobs', 'posted') ? 'jobs.posted' : "''") . ' as posted'),
            ]);

        if (Schema::hasTable('job_applications')) {
            $groupBy = ['jobs.id', 'jobs.title', 'jobs.company_name'];
            foreach (['type', 'status', 'posted'] as $column) {
                if (Schema::hasColumn('jobs', $column)) {
                    $groupBy[] = 'jobs.' . $column;
                }
            }

            $query->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
                ->addSelect(DB::raw('COUNT(job_applications.id) as applications'))
                ->groupBy(...$groupBy)
                ->orderByDesc('applications');
        } else {
            $query->addSelect(DB::raw('0 as applications'))->orderByDesc('jobs.created_at');
        }

        $rows = $query->limit(10)->get()->map(fn($job) => [
            $job->title ?: '-',
            $job->company_name ?: '-',
            $this->label($job->type ?: '-'),
            $this->label($job->status ?: 'active'),
            number_format($job->applications),
            $job->posted ?: '-',
        ])->values()->all();

        return $rows ?: [['No job data available', '-', '-', '-', '-', '-']];
    }

    private function workshopDetailRows(): array
    {
        if (!Schema::hasTable('workshops')) {
            return [['No workshop data available', '-', '-', '-', '-', '-']];
        }

        $capacityExpression = Schema::hasColumn('workshops', 'capacity')
            ? 'workshops.capacity'
            : (Schema::hasColumn('workshops', 'max_spots') ? 'workshops.max_spots' : '0');

        $query = DB::table('workshops')
            ->select([
                'workshops.id',
                'workshops.title',
                'workshops.date',
                'workshops.location',
                'workshops.status',
                DB::raw($capacityExpression . ' as capacity'),
            ]);

        if (Schema::hasTable('workshop_registrations')) {
            $groupBy = ['workshops.id', 'workshops.title', 'workshops.date', 'workshops.location', 'workshops.status'];
            if (Schema::hasColumn('workshops', 'capacity')) {
                $groupBy[] = 'workshops.capacity';
            } elseif (Schema::hasColumn('workshops', 'max_spots')) {
                $groupBy[] = 'workshops.max_spots';
            }

            $query->leftJoin('workshop_registrations', 'workshop_registrations.workshop_id', '=', 'workshops.id')
                ->addSelect(DB::raw('COUNT(workshop_registrations.id) as registrations'))
                ->groupBy(...$groupBy)
                ->orderByDesc('registrations');
        } else {
            $query->addSelect(DB::raw('0 as registrations'))->orderByDesc('workshops.created_at');
        }

        $rows = $query->limit(10)->get()->map(fn($workshop) => [
            $workshop->title ?: '-',
            $workshop->date ?: '-',
            $workshop->location ?: '-',
            $this->label($workshop->status ?: 'upcoming'),
            number_format($workshop->registrations),
            number_format($workshop->capacity),
        ])->values()->all();

        return $rows ?: [['No workshop data available', '-', '-', '-', '-', '-']];
    }

    private function scholarshipRows(): array
    {
        if (!Schema::hasTable('scholarships')) {
            return [['No scholarship data available', '-', '-', '-', '-']];
        }

        $query = DB::table('scholarships')
            ->select([
                'scholarships.id',
                'scholarships.title',
                DB::raw((Schema::hasColumn('scholarships', 'amount') ? 'scholarships.amount' : "''") . ' as amount'),
                DB::raw((Schema::hasColumn('scholarships', 'deadline') ? 'scholarships.deadline' : "''") . ' as deadline'),
                DB::raw((Schema::hasColumn('scholarships', 'status') ? 'scholarships.status' : "''") . ' as status'),
            ]);

        if (Schema::hasTable('scholarship_applications')) {
            $groupBy = ['scholarships.id', 'scholarships.title'];
            foreach (['amount', 'deadline', 'status'] as $column) {
                if (Schema::hasColumn('scholarships', $column)) {
                    $groupBy[] = 'scholarships.' . $column;
                }
            }

            $query->leftJoin('scholarship_applications', 'scholarship_applications.scholarship_id', '=', 'scholarships.id')
                ->addSelect(DB::raw('COUNT(scholarship_applications.id) as applications'))
                ->groupBy(...$groupBy)
                ->orderByDesc('applications');
        } else {
            $query->addSelect(DB::raw('0 as applications'))->orderByDesc('scholarships.created_at');
        }

        $rows = $query->limit(10)->get()->map(fn($scholarship) => [
            $scholarship->title ?: '-',
            $scholarship->amount ?: '-',
            $scholarship->deadline ?: '-',
            $this->label($scholarship->status ?: 'open'),
            number_format($scholarship->applications),
        ])->values()->all();

        return $rows ?: [['No scholarship data available', '-', '-', '-', '-']];
    }

    private function companyIndustryRows(): array
    {
        if (!Schema::hasTable('company_profiles') || !Schema::hasColumn('company_profiles', 'industry')) {
            return [['Company industry data', '-', 'Not available']];
        }

        $total = DB::table('company_profiles')->count();
        $rows = DB::table('company_profiles')
            ->select('industry', DB::raw('COUNT(*) as total'))
            ->whereNotNull('industry')
            ->where('industry', '!=', '')
            ->groupBy('industry')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                $row->industry,
                number_format($row->total),
                $this->percent((int) $row->total, $total),
            ])
            ->values()
            ->all();

        return $rows ?: [['No company industry data yet', '0', '0%']];
    }

    private function recentAlumniRows(): array
    {
        $query = DB::table('users')
            ->where('users.role', 'alumni')
            ->select('users.name', 'users.email', 'users.created_at');

        if (Schema::hasTable('alumni_profiles')) {
            $query->leftJoin('alumni_profiles', 'alumni_profiles.user_id', '=', 'users.id')
                ->addSelect([
                    DB::raw((Schema::hasColumn('alumni_profiles', 'major') ? 'alumni_profiles.major' : "''") . ' as major'),
                    DB::raw((Schema::hasColumn('alumni_profiles', 'graduation_year') ? 'alumni_profiles.graduation_year' : "''") . ' as graduation_year'),
                ]);
        } else {
            $query->addSelect(DB::raw("'' as major"), DB::raw("'' as graduation_year"));
        }

        $rows = $query->orderByDesc('users.created_at')
            ->limit(10)
            ->get()
            ->map(fn($user) => [
                $user->name ?: '-',
                $user->email ?: '-',
                $user->major ?: '-',
                $user->graduation_year ?: '-',
                $user->created_at ? date('Y-m-d H:i', strtotime($user->created_at)) : '-',
            ])
            ->values()
            ->all();

        return $rows ?: [['No alumni accounts yet', '-', '-', '-', '-']];
    }

    private function openScholarshipsCount(): int
    {
        if (!Schema::hasTable('scholarships')) {
            return 0;
        }

        return Schema::hasColumn('scholarships', 'status')
            ? DB::table('scholarships')->where('status', 'open')->count()
            : DB::table('scholarships')->count();
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
