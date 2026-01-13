<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $alumniId  = DB::table('users')->where('email', 'alumni@ptc.edu')->value('id');
        $collegeId = DB::table('users')->where('email', 'college@ptc.edu')->value('id');
        $companyId = DB::table('users')->where('email', 'company@techcorp.com')->value('id');



        if (!$alumniId || !$collegeId || !$companyId) {

        
            return;
        }

        // ======================
        // Jobs
        // ======================
        DB::table('job_applications')->truncate();
        DB::table('jobs')->truncate();

        $jobs = [
            [
                'company_user_id'=>$companyId,'title'=>'Frontend Developer','company_name'=>'TechCorp','location'=>'Gaza',
                'type'=>'Full-time','salary'=>'$800-$1200','posted'=>'2 days ago',
                'description'=>'Looking for an experienced React developer to join our team.',
                'status'=>'active','views'=>156
            ],
            [
                'company_user_id'=>$companyId,'title'=>'Software Engineer','company_name'=>'StartupX','location'=>'Remote',
                'type'=>'Full-time','salary'=>'$1000-$1500','posted'=>'3 days ago',
                'description'=>'Build scalable backend systems using Node.js and PostgreSQL.',
                'status'=>'active','views'=>98
            ],
            [
                'company_user_id'=>$companyId,'title'=>'UI/UX Designer','company_name'=>'DesignHub','location'=>'Ramallah',
                'type'=>'Part-time','salary'=>'$500-$800','posted'=>'5 days ago',
                'description'=>'Create beautiful user interfaces for web and mobile applications.',
                'status'=>'active','views'=>67
            ],
            [
                'company_user_id'=>$companyId,'title'=>'Data Analyst','company_name'=>'DataCo','location'=>'Gaza',
                'type'=>'Full-time','salary'=>'$700-$1100','posted'=>'1 week ago',
                'description'=>'Analyze data and create insights for business decisions.',
                'status'=>'active','views'=>80
            ],
            [
                'company_user_id'=>$companyId,'title'=>'Mobile Developer','company_name'=>'AppWorks','location'=>'Remote',
                'type'=>'Contract','salary'=>'$900-$1300','posted'=>'1 week ago',
                'description'=>'Develop cross-platform mobile applications using React Native.',
                'status'=>'active','views'=>55
            ],
        ];

        foreach ($jobs as $j) {
            DB::table('jobs')->insert(array_merge($j, [
                'created_at'=>now(), 'updated_at'=>now(),
            ]));
        }

        $jobIdFrontend = DB::table('jobs')->where('title','Frontend Developer')->value('id');
        $jobIdSoftware  = DB::table('jobs')->where('title','Software Engineer')->value('id');
        $jobIdUIUX      = DB::table('jobs')->where('title','UI/UX Designer')->value('id');
        $jobIdData      = DB::table('jobs')->where('title','Data Analyst')->value('id');

        DB::table('job_applications')->insert([
            ['job_id'=>$jobIdFrontend,'alumni_user_id'=>$alumniId,'status'=>'pending','applied_date'=>'Dec 20, 2025','created_at'=>now(),'updated_at'=>now()],
            ['job_id'=>$jobIdSoftware,'alumni_user_id'=>$alumniId,'status'=>'reviewed','applied_date'=>'Dec 18, 2025','created_at'=>now(),'updated_at'=>now()],
            ['job_id'=>$jobIdUIUX,'alumni_user_id'=>$alumniId,'status'=>'accepted','applied_date'=>'Dec 10, 2025','created_at'=>now(),'updated_at'=>now()],
            ['job_id'=>$jobIdData,'alumni_user_id'=>$alumniId,'status'=>'rejected','applied_date'=>'Dec 5, 2025','created_at'=>now(),'updated_at'=>now()],
        ]);

        // ======================
        // Workshops
        // ======================
        DB::table('workshop_registrations')->truncate();
        DB::table('workshops')->truncate();

        $workshops = [
            ['organizer_user_id'=>$collegeId,'organizer_role'=>'college','title'=>'Career Development Workshop','date'=>'Jan 15, 2026','time'=>'10:00 AM - 2:00 PM','location'=>'Main Campus, Hall A','spots'=>15,'max_spots'=>50,'status'=>'upcoming'],
            ['organizer_user_id'=>$collegeId,'organizer_role'=>'college','title'=>'Technical Interview Prep','date'=>'Jan 20, 2026','time'=>'2:00 PM - 5:00 PM','location'=>'Online (Zoom)','spots'=>30,'max_spots'=>100,'status'=>'upcoming'],
            ['organizer_user_id'=>$collegeId,'organizer_role'=>'college','title'=>'Resume Writing Masterclass','date'=>'Jan 25, 2026','time'=>'11:00 AM - 1:00 PM','location'=>'Main Campus, Lab B','spots'=>5,'max_spots'=>30,'status'=>'upcoming'],
            ['organizer_user_id'=>$collegeId,'organizer_role'=>'college','title'=>'Networking Skills for Professionals','date'=>'Feb 1, 2026','time'=>'3:00 PM - 6:00 PM','location'=>'Community Center','spots'=>25,'max_spots'=>40,'status'=>'upcoming'],
            ['organizer_user_id'=>$collegeId,'organizer_role'=>'college','title'=>'Entrepreneurship Basics','date'=>'Feb 10, 2026','time'=>'9:00 AM - 12:00 PM','location'=>'Online (Teams)','spots'=>50,'max_spots'=>200,'status'=>'upcoming'],
        ];

        foreach ($workshops as $w) {
            DB::table('workshops')->insert(array_merge($w, [
                'created_at'=>now(), 'updated_at'=>now(),
            ]));
        }

        $workshopIdCareer = DB::table('workshops')->where('title','Career Development Workshop')->value('id');
        $workshopIdNetworking = DB::table('workshops')->where('title','Networking Skills for Professionals')->value('id');

        DB::table('workshop_registrations')->insert([
            ['workshop_id'=>$workshopIdCareer,'alumni_user_id'=>$alumniId,'status'=>'registered','created_at'=>now(),'updated_at'=>now()],
            ['workshop_id'=>$workshopIdNetworking,'alumni_user_id'=>$alumniId,'status'=>'registered','created_at'=>now(),'updated_at'=>now()],
        ]);

        // ======================
        // Scholarships
        // ======================
        DB::table('scholarship_applications')->truncate();
        DB::table('scholarships')->truncate();

        $scholarships = [
            ['created_by_user_id'=>$collegeId,'title'=>'Graduate Excellence Award','amount'=>'$5,000','deadline'=>'Feb 15, 2026','description'=>'Scholarship for high-achieving graduates.','requirements'=>'GPA 3.5+ • Active participation','status'=>'open'],
            ['created_by_user_id'=>$collegeId,'title'=>'Tech Innovation Scholarship','amount'=>'$3,000','deadline'=>'Mar 1, 2026','description'=>'Support innovative tech projects.','requirements'=>'Tech-related project submission','status'=>'open'],
            ['created_by_user_id'=>$collegeId,'title'=>'Community Leadership Grant','amount'=>'$2,500','deadline'=>'Mar 15, 2026','description'=>'For outstanding community leaders.','requirements'=>'Community service record','status'=>'open'],
            ['created_by_user_id'=>$collegeId,'title'=>'Research Excellence Fund','amount'=>'$4,000','deadline'=>'Jan 30, 2026','description'=>'Funding for research excellence.','requirements'=>'Published research paper','status'=>'closing_soon'],
        ];

        foreach ($scholarships as $s) {
            DB::table('scholarships')->insert(array_merge($s, [
                'created_at'=>now(), 'updated_at'=>now(),
            ]));
        }

        $schId1 = DB::table('scholarships')->where('title','Graduate Excellence Award')->value('id');
        DB::table('scholarship_applications')->insert([
            ['scholarship_id'=>$schId1,'alumni_user_id'=>$alumniId,'status'=>'accepted','applied_date'=>'Dec 22, 2025','created_at'=>now(),'updated_at'=>now()],
        ]);

        // ======================
        // Recommendations
        // ======================
        DB::table('recommendations')->truncate();
        DB::table('recommendations')->insert([
            [
                'from_user_id'=>null,'to_user_id'=>$alumniId,
                'from_name'=>'Sara Ali','to_name'=>'Demo Alumni','role_title'=>'Senior Developer at TechCorp',
                'content'=>'Ahmed is an exceptional developer with great problem-solving skills. Highly recommended!',
                'date'=>'Dec 15, 2025',
                'created_at'=>now(),'updated_at'=>now()
            ],
            [
                'from_user_id'=>null,'to_user_id'=>$alumniId,
                'from_name'=>'Omar Khalil','to_name'=>'Demo Alumni','role_title'=>'Project Manager at StartupX',
                'content'=>'Worked with Ahmed on multiple projects. Very professional and reliable team member.',
                'date'=>'Nov 28, 2025',
                'created_at'=>now(),'updated_at'=>now()
            ],
            [
                'from_user_id'=>$alumniId,'to_user_id'=>null,
                'from_name'=>'Demo Alumni','to_name'=>'Layla Hassan','role_title'=>'UI/UX Designer',
                'content'=>'Layla has an amazing eye for design and user experience. A pleasure to work with!',
                'date'=>'Dec 10, 2025',
                'created_at'=>now(),'updated_at'=>now()
            ],
        ]);

        // ======================
        // Leaderboard
        // ======================
        DB::table('leaderboard_entries')->truncate();
        $leaders = [
            ['rank'=>1,'points'=>1250,'activities'=>45,'trend'=>'+120'],
            ['rank'=>2,'points'=>1180,'activities'=>42,'trend'=>'+95'],
            ['rank'=>3,'points'=>1050,'activities'=>38,'trend'=>'+80'],
            ['rank'=>4,'points'=>980,'activities'=>35,'trend'=>'+65'],
            ['rank'=>5,'points'=>920,'activities'=>33,'trend'=>'+55'],
            ['rank'=>6,'points'=>870,'activities'=>30,'trend'=>'+50'],
            ['rank'=>7,'points'=>850,'activities'=>28,'trend'=>'+45'],
            ['rank'=>8,'points'=>820,'activities'=>27,'trend'=>'+40'],
        ];

        foreach ($leaders as $l) {
            DB::table('leaderboard_entries')->insert([
                'alumni_user_id' => $alumniId,
                'rank' => $l['rank'],
                'points' => $l['points'],
                'activities' => $l['activities'],
                'trend' => $l['trend'],
                'period' => 'monthly',
                'created_at'=>now(), 'updated_at'=>now(),
            ]);
        }
    }
}
