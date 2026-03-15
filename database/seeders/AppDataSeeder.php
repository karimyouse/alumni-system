<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppDataSeeder extends Seeder
{
    public function run(): void
    {
        $collegeId = DB::table('users')->where('email', 'college@ptc.edu')->value('id');
        $companyId = DB::table('users')->where('email', 'company@techcorp.com')->value('id');

        $primaryAlumniId = DB::table('users')->where('email', 'karim15062000@gmail.com')->value('id');
        $aliId = DB::table('users')->where('email', 'ali@gmail.com')->value('id');
        $ahmedOldId = DB::table('users')->where('email', 'ahmed@gmail.com')->value('id');
        $ahmedSalehId = DB::table('users')->where('email', 'ahmedsaleh@gmail.com')->value('id');
        $ahmedAmmraId = DB::table('users')->where('email', 'ahmedammra@gmail.com')->value('id');
        $mohammedId = DB::table('users')->where('email', 'mohammed@gmail.com')->value('id');

        $alumniPool = array_values(array_filter([
            $primaryAlumniId,
            $aliId,
            $ahmedOldId,
            $ahmedSalehId,
            $ahmedAmmraId,
            $mohammedId,
        ]));

        if (!$collegeId || !$companyId || empty($alumniPool)) {
            return;
        }

        $primaryRecommendationUserId = $primaryAlumniId ?: $alumniPool[0];

        $truncate = function (string $table) {
            if (!Schema::hasTable($table)) {
                return;
            }

            DB::table($table)->truncate();
        };

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } catch (\Throwable $e) {
        }

        $truncate('job_applications');
        $truncate('saved_jobs');
        $truncate('jobs');

        $truncate('workshop_registrations');
        $truncate('workshops');

        $truncate('scholarship_applications');
        $truncate('scholarships');

        $truncate('recommendations');
        $truncate('leaderboard_entries');

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $e) {
        }

        $jobs = [
            [
                'company_user_id' => $companyId,
                'title' => 'Frontend Developer',
                'company_name' => 'TechCorp',
                'location' => 'Gaza',
                'type' => 'Full-time',
                'salary' => '$800-$1200',
                'posted' => '2 days ago',
                'description' => 'Looking for an experienced React developer to join our team.',
                'status' => 'active',
                'views' => 156,
                'approval_status' => 'approved',
            ],
            [
                'company_user_id' => $companyId,
                'title' => 'Software Engineer',
                'company_name' => 'StartupX',
                'location' => 'Remote',
                'type' => 'Full-time',
                'salary' => '$1000-$1500',
                'posted' => '3 days ago',
                'description' => 'Build scalable backend systems using Node.js and PostgreSQL.',
                'status' => 'active',
                'views' => 98,
                'approval_status' => 'approved',
            ],
            [
                'company_user_id' => $companyId,
                'title' => 'UI/UX Designer',
                'company_name' => 'DesignHub',
                'location' => 'Ramallah',
                'type' => 'Part-time',
                'salary' => '$500-$800',
                'posted' => '5 days ago',
                'description' => 'Create beautiful user interfaces for web and mobile applications.',
                'status' => 'active',
                'views' => 67,
                'approval_status' => 'approved',
            ],
            [
                'company_user_id' => $companyId,
                'title' => 'Data Analyst',
                'company_name' => 'DataCo',
                'location' => 'Gaza',
                'type' => 'Full-time',
                'salary' => '$700-$1100',
                'posted' => '1 week ago',
                'description' => 'Analyze data and create insights for business decisions.',
                'status' => 'active',
                'views' => 80,
                'approval_status' => 'approved',
            ],
            [
                'company_user_id' => $companyId,
                'title' => 'Mobile Developer',
                'company_name' => 'AppWorks',
                'location' => 'Remote',
                'type' => 'Contract',
                'salary' => '$900-$1300',
                'posted' => '1 week ago',
                'description' => 'Develop cross-platform mobile applications using React Native.',
                'status' => 'active',
                'views' => 55,
                'approval_status' => 'pending',
            ],
        ];

        foreach ($jobs as $j) {
            $row = [
                'company_user_id' => $j['company_user_id'],
                'title' => $j['title'],
                'company_name' => $j['company_name'],
                'location' => $j['location'],
                'type' => $j['type'],
                'salary' => $j['salary'],
                'description' => $j['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('jobs', 'posted')) {
                $row['posted'] = $j['posted'];
            }

            if (Schema::hasColumn('jobs', 'status')) {
                $row['status'] = $j['status'];
            }

            if (Schema::hasColumn('jobs', 'views')) {
                $row['views'] = $j['views'];
            }

            if (Schema::hasColumn('jobs', 'approval_status')) {
                $row['approval_status'] = $j['approval_status'] ?? 'pending';

                if (Schema::hasColumn('jobs', 'approved_at')) {
                    $row['approved_at'] = ($row['approval_status'] === 'approved') ? now() : null;
                }

                if (Schema::hasColumn('jobs', 'approved_by')) {
                    $row['approved_by'] = ($row['approval_status'] === 'approved') ? $collegeId : null;
                }

                if (Schema::hasColumn('jobs', 'reject_reason')) {
                    $row['reject_reason'] = null;
                }

                if (Schema::hasColumn('jobs', 'is_featured')) {
                    $row['is_featured'] = false;
                }
            }

            DB::table('jobs')->insert($row);
        }

        $jobIdFrontend = DB::table('jobs')->where('title', 'Frontend Developer')->value('id');
        $jobIdSoftware = DB::table('jobs')->where('title', 'Software Engineer')->value('id');
        $jobIdUIUX = DB::table('jobs')->where('title', 'UI/UX Designer')->value('id');
        $jobIdData = DB::table('jobs')->where('title', 'Data Analyst')->value('id');

        if (Schema::hasTable('job_applications')) {
            $jobApplications = [
                [
                    'job_id' => $jobIdFrontend,
                    'alumni_user_id' => $alumniPool[0] ?? null,
                    'status' => 'pending',
                    'applied_date' => 'Dec 20, 2025',
                ],
                [
                    'job_id' => $jobIdSoftware,
                    'alumni_user_id' => $alumniPool[1] ?? ($alumniPool[0] ?? null),
                    'status' => 'reviewed',
                    'applied_date' => 'Dec 18, 2025',
                ],
                [
                    'job_id' => $jobIdUIUX,
                    'alumni_user_id' => $alumniPool[2] ?? ($alumniPool[0] ?? null),
                    'status' => 'accepted',
                    'applied_date' => 'Dec 10, 2025',
                ],
                [
                    'job_id' => $jobIdData,
                    'alumni_user_id' => $alumniPool[3] ?? ($alumniPool[0] ?? null),
                    'status' => 'rejected',
                    'applied_date' => 'Dec 5, 2025',
                ],
            ];

            $jobApplications = array_filter($jobApplications, fn ($item) => !empty($item['job_id']) && !empty($item['alumni_user_id']));

            foreach ($jobApplications as $application) {
                DB::table('job_applications')->insert([
                    'job_id' => $application['job_id'],
                    'alumni_user_id' => $application['alumni_user_id'],
                    'status' => $application['status'],
                    'applied_date' => $application['applied_date'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $workshops = [
            ['owner' => 'college', 'title' => 'Career Development Workshop', 'date' => 'Jan 15, 2026', 'time' => '10:00 AM - 2:00 PM', 'location' => 'Main Campus, Hall A', 'spots' => 15, 'max_spots' => 50, 'status' => 'upcoming'],
            ['owner' => 'college', 'title' => 'Technical Interview Prep', 'date' => 'Jan 20, 2026', 'time' => '2:00 PM - 5:00 PM', 'location' => 'Online (Zoom)', 'spots' => 30, 'max_spots' => 100, 'status' => 'upcoming'],
            ['owner' => 'college', 'title' => 'Resume Writing Masterclass', 'date' => 'Jan 25, 2026', 'time' => '11:00 AM - 1:00 PM', 'location' => 'Main Campus, Lab B', 'spots' => 5, 'max_spots' => 30, 'status' => 'upcoming'],
            ['owner' => 'college', 'title' => 'Networking Skills for Professionals', 'date' => 'Feb 1, 2026', 'time' => '3:00 PM - 6:00 PM', 'location' => 'Community Center', 'spots' => 25, 'max_spots' => 40, 'status' => 'upcoming'],
            ['owner' => 'college', 'title' => 'Entrepreneurship Basics', 'date' => 'Feb 10, 2026', 'time' => '9:00 AM - 12:00 PM', 'location' => 'Online (Teams)', 'spots' => 50, 'max_spots' => 200, 'status' => 'upcoming'],
            ['owner' => 'company', 'title' => 'Tech Career Day 2026', 'date' => 'Feb 15, 2026', 'time' => '10:00 AM - 2:00 PM', 'location' => 'Main Campus', 'spots' => 45, 'max_spots' => 45, 'status' => 'upcoming'],
            ['owner' => 'company', 'title' => 'Coding Bootcamp Introduction', 'date' => 'Jan 28, 2026', 'time' => '2:00 PM - 4:00 PM', 'location' => 'Online', 'spots' => 80, 'max_spots' => 80, 'status' => 'upcoming'],
        ];

        foreach ($workshops as $w) {
            $row = [
                'title' => $w['title'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('workshops', 'date')) {
                $row['date'] = $w['date'];
            }

            if (Schema::hasColumn('workshops', 'time')) {
                $row['time'] = $w['time'];
            }

            if (Schema::hasColumn('workshops', 'location')) {
                $row['location'] = $w['location'];
            }

            if (Schema::hasColumn('workshops', 'status')) {
                $row['status'] = $w['status'];
            }

            if (Schema::hasColumn('workshops', 'proposal_status')) {
                $row['proposal_status'] = 'approved';
            }

            if ($w['owner'] === 'college') {
                if (Schema::hasColumn('workshops', 'organizer_user_id')) {
                    $row['organizer_user_id'] = $collegeId;
                }

                if (Schema::hasColumn('workshops', 'organizer_role')) {
                    $row['organizer_role'] = 'college';
                }

                if (Schema::hasColumn('workshops', 'company_user_id')) {
                    $row['company_user_id'] = null;
                }
            } else {
                if (Schema::hasColumn('workshops', 'organizer_user_id')) {
                    $row['organizer_user_id'] = $companyId;
                }

                if (Schema::hasColumn('workshops', 'organizer_role')) {
                    $row['organizer_role'] = 'company';
                }

                if (Schema::hasColumn('workshops', 'company_user_id')) {
                    $row['company_user_id'] = $companyId;
                }
            }

            if (Schema::hasColumn('workshops', 'max_spots')) {
                $row['max_spots'] = (int) $w['max_spots'];
            }

            if (Schema::hasColumn('workshops', 'spots')) {
                $row['spots'] = (int) $w['spots'];
            }

            if (Schema::hasColumn('workshops', 'capacity')) {
                $row['capacity'] = ((int) $w['max_spots'] > 0) ? (int) $w['max_spots'] : null;
            }

            DB::table('workshops')->insert($row);
        }

        $workshopIdCareer = DB::table('workshops')->where('title', 'Career Development Workshop')->value('id');
        $workshopIdNetworking = DB::table('workshops')->where('title', 'Networking Skills for Professionals')->value('id');

        if (Schema::hasTable('workshop_registrations')) {
            $workshopRegistrations = [
                [
                    'workshop_id' => $workshopIdCareer,
                    'alumni_user_id' => $alumniPool[0] ?? null,
                    'status' => 'registered',
                ],
                [
                    'workshop_id' => $workshopIdNetworking,
                    'alumni_user_id' => $alumniPool[1] ?? ($alumniPool[0] ?? null),
                    'status' => 'registered',
                ],
            ];

            $workshopRegistrations = array_filter($workshopRegistrations, fn ($item) => !empty($item['workshop_id']) && !empty($item['alumni_user_id']));

            foreach ($workshopRegistrations as $registration) {
                DB::table('workshop_registrations')->insert([
                    'workshop_id' => $registration['workshop_id'],
                    'alumni_user_id' => $registration['alumni_user_id'],
                    'status' => $registration['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $scholarships = [
            ['created_by_user_id' => $collegeId, 'title' => 'Graduate Excellence Award', 'amount' => '$5,000', 'deadline' => 'Feb 15, 2026', 'description' => 'Scholarship for high-achieving graduates.', 'requirements' => 'GPA 3.5+ • Active participation', 'status' => 'open'],
            ['created_by_user_id' => $collegeId, 'title' => 'Tech Innovation Scholarship', 'amount' => '$3,000', 'deadline' => 'Mar 1, 2026', 'description' => 'Support innovative tech projects.', 'requirements' => 'Tech-related project submission', 'status' => 'open'],
            ['created_by_user_id' => $collegeId, 'title' => 'Community Leadership Grant', 'amount' => '$2,500', 'deadline' => 'Mar 15, 2026', 'description' => 'For outstanding community leaders.', 'requirements' => 'Community service record', 'status' => 'open'],
            ['created_by_user_id' => $collegeId, 'title' => 'Research Excellence Fund', 'amount' => '$4,000', 'deadline' => 'Jan 30, 2026', 'description' => 'Funding for research excellence.', 'requirements' => 'Published research paper', 'status' => 'closing_soon'],
        ];

        foreach ($scholarships as $s) {
            DB::table('scholarships')->insert(array_merge($s, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $schId1 = DB::table('scholarships')->where('title', 'Graduate Excellence Award')->value('id');

        if (Schema::hasTable('scholarship_applications') && $schId1) {
            DB::table('scholarship_applications')->insert([
                'scholarship_id' => $schId1,
                'alumni_user_id' => $alumniPool[0],
                'status' => 'accepted',
                'applied_date' => 'Dec 22, 2025',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('recommendations')) {
            DB::table('recommendations')->insert([
                [
                    'from_user_id' => null,
                    'to_user_id' => $primaryRecommendationUserId,
                    'from_name' => 'Sara Ali',
                    'to_name' => 'Karim Shafiq Yousef',
                    'role_title' => 'Senior Developer at TechCorp',
                    'content' => 'Ahmed is an exceptional developer with great problem-solving skills. Highly recommended!',
                    'date' => 'Dec 15, 2025',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'from_user_id' => null,
                    'to_user_id' => $primaryRecommendationUserId,
                    'from_name' => 'Omar Khalil',
                    'to_name' => 'Karim Shafiq Yousef',
                    'role_title' => 'Project Manager at StartupX',
                    'content' => 'Worked with Ahmed on multiple projects. Very professional and reliable team member.',
                    'date' => 'Nov 28, 2025',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'from_user_id' => $primaryRecommendationUserId,
                    'to_user_id' => null,
                    'from_name' => 'Karim Shafiq Yousef',
                    'to_name' => 'Layla Hassan',
                    'role_title' => 'UI/UX Designer',
                    'content' => 'Layla has an amazing eye for design and user experience. A pleasure to work with!',
                    'date' => 'Dec 10, 2025',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Schema::hasTable('leaderboard_entries')) {
            $leaders = [
                ['rank' => 1, 'points' => 1250, 'activities' => 45, 'trend' => '+120'],
                ['rank' => 2, 'points' => 1180, 'activities' => 42, 'trend' => '+95'],
                ['rank' => 3, 'points' => 1050, 'activities' => 38, 'trend' => '+80'],
                ['rank' => 4, 'points' => 980, 'activities' => 35, 'trend' => '+65'],
                ['rank' => 5, 'points' => 920, 'activities' => 33, 'trend' => '+55'],
                ['rank' => 6, 'points' => 870, 'activities' => 30, 'trend' => '+50'],
                ['rank' => 7, 'points' => 850, 'activities' => 28, 'trend' => '+45'],
                ['rank' => 8, 'points' => 820, 'activities' => 27, 'trend' => '+40'],
            ];

            foreach ($leaders as $index => $l) {
                $leaderUserId = $alumniPool[$index % count($alumniPool)];

                DB::table('leaderboard_entries')->insert([
                    'alumni_user_id' => $leaderUserId,
                    'points' => $l['points'],
                    'activities' => $l['activities'],
                    'rank' => $l['rank'],
                    'trend' => $l['trend'],
                    'period' => 'monthly',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
