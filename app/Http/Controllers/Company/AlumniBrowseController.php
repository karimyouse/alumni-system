<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\Request;

class AlumniBrowseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $major = trim((string)$request->query('major', ''));
        $location = trim((string)$request->query('location', ''));
        $skill = trim((string)$request->query('skill', ''));

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

        if ($major !== '') {
            $query->whereHas('alumniProfile', fn($p) => $p->where('major', 'like', "%{$major}%"));
        }

        if ($location !== '') {
            $query->whereHas('alumniProfile', fn($p) => $p->where('location', 'like', "%{$location}%"));
        }

        if ($skill !== '') {
            // skills stored comma-separated in alumni_profiles.skills
            $query->whereHas('alumniProfile', fn($p) => $p->where('skills', 'like', "%{$skill}%"));
        }

        $alumni = $query->orderBy('name')->paginate(10)->withQueryString();

        // simple filter options (from DB)
        $majors = AlumniProfile::query()->whereNotNull('major')->where('major','!=','')->distinct()->pluck('major')->sort()->values();
        $locations = AlumniProfile::query()->whereNotNull('location')->where('location','!=','')->distinct()->pluck('location')->sort()->values();

        return view('company.alumni-browse', compact('alumni', 'q', 'major', 'location', 'skill', 'majors', 'locations'));
    }

    public function show(User $alumnus)
    {
        if ($alumnus->role !== 'alumni') abort(404);

        $alumnus->load('alumniProfile');

        return view('company.alumni-show', compact('alumnus'));
    }
}
