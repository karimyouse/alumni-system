<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\Scholarship;
use App\Models\SuccessStory;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $query = User::query()
            ->where('role', 'alumni')
            ->with('alumniProfile');

        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('academic_id', 'like', "%{$q}%");
            });
        }

        $alumni = $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('college.alumni-management', array_merge(
            compact('alumni', 'q'),
            $this->buildNavCounts()
        ));
    }

    public function show(User $alumnus)
    {
        if ($alumnus->role !== 'alumni') {
            abort(404);
        }

        $alumnus->load('alumniProfile');

        return view('college.alumni-show', array_merge(
            compact('alumnus'),
            $this->buildNavCounts()
        ));
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
