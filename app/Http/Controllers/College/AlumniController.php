<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
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
        $search = trim((string) $request->query('search', $request->query('q', '')));
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
            $profile = $user->alumniProfile ?? new AlumniProfile();
            $skills = $this->parseSkills($profile);
            $status = $this->resolveAvailabilityStatus($profile);

            $user->display_initials = $this->initials($user->name);
            $user->display_photo_url = !empty($profile->profile_photo)
                ? asset('storage/' . ltrim($profile->profile_photo, '/'))
                : null;
            $user->display_major_year = trim((string) ($profile->major ?? '-')) . ' (' . trim((string) ($profile->graduation_year ?? '-')) . ')';
            $user->display_location = $profile->location ?: '-';
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

        return view('college.alumni-management', array_merge(
            compact('alumni', 'search', 'major', 'location', 'skill', 'majors', 'locations'),
            $this->buildNavCounts()
        ));
    }

    public function show(User $alumnus)
    {
        if ($alumnus->role !== 'alumni') {
            abort(404);
        }

        $alumnus->load('alumniProfile');
        $profile = $alumnus->alumniProfile ?? new AlumniProfile();
        $skills = $this->parseSkills($profile);
        $initials = $this->initials($alumnus->name);
        $status = $this->resolveAvailabilityStatus($profile);

        return view('college.alumni-show', array_merge(
            [
                'alumnus' => $alumnus,
                'profile' => $profile,
                'skills' => $skills,
                'initials' => $initials,
                'photoUrl' => !empty($profile->profile_photo)
                    ? asset('storage/' . ltrim($profile->profile_photo, '/'))
                    : null,
                'statusLabel' => $status['label'],
                'statusClass' => $status['class'],
            ],
            $this->buildNavCounts()
        ));
    }

    private function parseSkills(?AlumniProfile $profile)
    {
        return collect(explode(',', (string) ($profile->skills ?? '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values();
    }

    private function initials(?string $name): string
    {
        return collect(explode(' ', (string) $name))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->join('') ?: 'A';
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
