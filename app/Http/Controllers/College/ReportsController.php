<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use App\Models\Announcement;
use App\Models\SuccessStory;
use App\Models\Scholarship;
use App\Models\Job;
use Illuminate\Support\Facades\Schema;

class ReportsController extends Controller
{
    public function index()
    {
        $stats = [
            'alumni' => User::where('role','alumni')->count(),
            'companies' => User::where('role','company')->count(),
            'college_users' => User::where('role','college')->count(),
            'admins' => User::whereIn('role',['admin','super_admin'])->count(),

            'workshops' => Schema::hasTable('workshops') ? Workshop::count() : 0,
            'announcements' => Schema::hasTable('announcements') ? Announcement::count() : 0,
            'stories' => Schema::hasTable('success_stories') ? SuccessStory::count() : 0,
            'scholarships' => (class_exists(Scholarship::class) && Schema::hasTable('scholarships')) ? Scholarship::count() : 0,
            'jobs' => (class_exists(Job::class) && Schema::hasTable('jobs')) ? Job::count() : 0,
        ];

        // optional: published counts
        $published = [
            'announcements' => Schema::hasTable('announcements')
                ? Announcement::where('is_published', true)->count()
                : 0,
            'stories' => Schema::hasTable('success_stories')
                ? SuccessStory::where('is_published', true)->count()
                : 0,
        ];

        return view('college.reports', compact('stats', 'published'));
    }
}
