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
        $search = trim((string) $request->query('search', ''));
        $major = trim((string) $request->query('major', ''));
        $location = trim((string) $request->query('location', ''));
        $skill = trim((string) $request->query('skill', ''));

        $query = User::query()
            ->where('role', 'alumni')
            ->with('alumniProfile')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('academic_id', 'like', "%{$search}%");
            });
        }

        if ($major !== '') {
            $query->whereHas('alumniProfile', function ($q) use ($major) {
                $q->where('major', $major);
            });
        }

        if ($location !== '') {
            $query->whereHas('alumniProfile', function ($q) use ($location) {
                $q->where('location', $location);
            });
        }

        if ($skill !== '') {
            $query->whereHas('alumniProfile', function ($q) use ($skill) {
                $q->where('skills', 'like', "%{$skill}%");
            });
        }

        $alumni = $query->paginate(12)->withQueryString();

        $alumni->getCollection()->transform(function ($user) {
            $profile = $user->alumniProfile;

            $skills = collect(explode(',', (string) ($profile->skills ?? '')))
                ->map(fn ($s) => trim($s))
                ->filter()
                ->values();

            $initials = collect(explode(' ', (string) $user->name))
                ->filter()
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->join('') ?: 'A';

            $status = $this->resolveAvailabilityStatus($profile);

            $user->display_initials = $initials;
            $user->display_major_year = trim((string) ($profile->major ?? '—')) . ' (' . trim((string) ($profile->graduation_year ?? '—')) . ')';
            $user->display_location = $profile->location ?: '—';
            $user->display_skills = $skills->take(4)->values();
            $user->display_status_label = $status['label'];
            $user->display_status_class = $status['class'];

            return $user;
        });

        $majors = AlumniProfile::query()
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major')
            ->values();

        $locations = AlumniProfile::query()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location')
            ->values();

        return view('company.alumni-browse', [
            'alumni' => $alumni,
            'search' => $search,
            'major' => $major,
            'location' => $location,
            'skill' => $skill,
            'majors' => $majors,
            'locations' => $locations,
        ]);
    }

    public function show(User $alumnus)
    {
        abort_unless($alumnus->role === 'alumni', 404);

        $alumnus->load('alumniProfile');
        $profile = $alumnus->alumniProfile;

        $skills = collect(explode(',', (string) ($profile->skills ?? '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values();

        $initials = collect(explode(' ', (string) $alumnus->name))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->join('') ?: 'A';

        $status = $this->resolveAvailabilityStatus($profile);

        return view('company.alumni-show', [
            'alumnus' => $alumnus,
            'profile' => $profile,
            'skills' => $skills,
            'initials' => $initials,
            'statusLabel' => $status['label'],
            'statusClass' => $status['class'],
        ]);
    }

    private function resolveAvailabilityStatus(?AlumniProfile $profile): array
    {
        $employment = strtolower(trim((string) ($profile->employment_status ?? '')));

        return match ($employment) {
            'employed' => [
                'label' => 'Employed',
                'class' => 'bg-secondary text-secondary-foreground',
            ],
            'available', 'unemployed', 'seeking' => [
                'label' => 'Available',
                'class' => 'bg-green-500/10 text-green-400',
            ],
            default => [
                'label' => 'Available',
                'class' => 'bg-green-500/10 text-green-400',
            ],
        };
    }
}
