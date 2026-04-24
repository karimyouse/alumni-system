<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Auth::user();
        $companyId = (int) $company->id;
        $companyProfile = $company->companyProfile;

        $jobsQuery = Job::query()
            ->where('organizer_role', 'company')
            ->where('company_user_id', $companyId);

        $jobsCount = (clone $jobsQuery)->count();
        $activeJobsCount = (clone $jobsQuery)->where('status', 'active')->count();

        $jobIds = (clone $jobsQuery)->pluck('id');

        $applicationsQuery = JobApplication::query()->whereIn('job_id', $jobIds);

        $applicationsCount = (clone $applicationsQuery)->count();
        $pendingCount = (clone $applicationsQuery)->where('status', 'pending')->count();
        $reviewedCount = (clone $applicationsQuery)->where('status', 'reviewed')->count();
        $acceptedCount = (clone $applicationsQuery)->where('status', 'accepted')->count();
        $rejectedCount = (clone $applicationsQuery)->where('status', 'rejected')->count();

        $profileViews = 0;
        if (Schema::hasColumn('jobs', 'views')) {
            $profileViews = (int) ((clone $jobsQuery)->sum('views') ?? 0);
        }

        $uniqueApplicants = (clone $applicationsQuery)
            ->distinct('alumni_user_id')
            ->count('alumni_user_id');

        $latestJobs = (clone $jobsQuery)
            ->orderByDesc('id')
            ->take(3)
            ->get()
            ->map(function ($job) {
                $job->applications_count = JobApplication::where('job_id', $job->id)->count();
                $job->display_views = Schema::hasColumn('jobs', 'views') ? (int) ($job->views ?? 0) : 0;
                $job->display_posted = $job->posted ?: ($job->created_at ? $job->created_at->format('M j, Y') : '—');
                $job->display_status = ucfirst((string) ($job->status ?? 'active'));
                return $job;
            });

        $recentApplications = (clone $applicationsQuery)
            ->with(['alumni.alumniProfile', 'job'])
            ->orderByDesc('id')
            ->take(4)
            ->get()
            ->map(function ($application) {
                $name = $application->alumni?->name ?? 'Alumni';
                $application->candidate_initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('') ?: 'A';

                $application->candidate_name = $name;
                $application->candidate_photo_url = !empty($application->alumni?->alumniProfile?->profile_photo)
                    ? asset('storage/' . ltrim($application->alumni->alumniProfile->profile_photo, '/'))
                    : null;
                $application->job_title = $application->job?->title ?? 'Job';
                $application->display_status = ucfirst((string) ($application->status ?? 'pending'));

                $application->status_class = match ($application->status) {
                    'accepted' => 'bg-green-500/10 text-green-400',
                    'rejected' => 'bg-red-500/10 text-red-400',
                    'reviewed' => 'bg-blue-500/10 text-blue-400',
                    default => 'bg-yellow-500/10 text-yellow-400',
                };

                return $application;
            });

        $recommendedCandidates = User::query()
            ->where('role', 'alumni')
            ->with('alumniProfile')
            ->get()
            ->map(function ($user) {
                $profile = $user->alumniProfile;

                $skills = collect(explode(',', (string) ($profile->skills ?? '')))
                    ->map(fn ($s) => trim($s))
                    ->filter()
                    ->values();

                $completionChecks = [
                    !empty($user->name),
                    !empty($user->email),
                    !empty($user->academic_id),
                    !empty($profile?->phone),
                    !empty($profile?->location),
                    !empty($profile?->major),
                    !empty($profile?->graduation_year),
                    !empty($profile?->bio),
                    $skills->isNotEmpty(),
                    !empty($profile?->linkedin),
                    !empty($profile?->portfolio),
                ];

                $completed = collect($completionChecks)->filter()->count();
                $profileStrength = (int) round(($completed / count($completionChecks)) * 100);

                $initials = collect(explode(' ', (string) $user->name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('') ?: 'A';

                return [
                    'id' => $user->id,
                    'name' => $user->name ?? 'Alumni',
                    'initials' => $initials,
                    'photo_url' => !empty($profile?->profile_photo)
                        ? asset('storage/' . ltrim($profile->profile_photo, '/'))
                        : null,
                    'graduation_year' => $profile->graduation_year ?? '—',
                    'skills' => $skills->take(3)->values(),
                    'profile_strength' => min($profileStrength, 100),
                ];
            })
            ->sortByDesc('profile_strength')
            ->take(3)
            ->values();

        return view('company.index', array_merge([
            'companyName' => $company->name ?? 'Company',
            'companyProfile' => $companyProfile,
            'jobsCount' => $jobsCount,
            'activeJobsCount' => $activeJobsCount,
            'applicationsCount' => $applicationsCount,
            'pendingCount' => $pendingCount,
            'reviewedCount' => $reviewedCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'profileViews' => $profileViews,
            'uniqueApplicants' => $uniqueApplicants,
            'latestJobs' => $latestJobs,
            'recentApplications' => $recentApplications,
            'recommendedCandidates' => $recommendedCandidates,
        ], $this->buildNavCounts()));
    }

    public function editProfile()
    {
        $company = Auth::user();
        $companyProfile = $company->companyProfile;

        return view('company.profile', array_merge([
            'companyName' => $company->name ?? 'Company',
            'companyProfile' => $companyProfile,
        ], $this->buildNavCounts()));
    }

    public function updateProfile(Request $request)
    {
        $company = Auth::user();

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($company->id)],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = CompanyProfile::firstOrNew(['user_id' => $company->id]);
        $existingStatus = $profile->exists ? ($profile->status ?? 'approved') : 'approved';

        $profile->fill([
            'company_name' => $data['company_name'],
            'contact_person_name' => $data['contact_person_name'] ?? null,
            'industry' => $data['industry'] ?? null,
            'location' => $data['location'] ?? null,
            'website' => $data['website'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $existingStatus,
        ]);

        $profile->save();

        $normalizedEmail = strtolower(trim((string) $data['email']));
        $emailChanged = $normalizedEmail !== (string) $company->email;

        $userUpdate = [
            'name' => $data['company_name'],
            'email' => $normalizedEmail,
            'email_verified_at' => $emailChanged ? null : $company->email_verified_at,
        ];

        if ($request->hasFile('profile_photo')) {
            if (!empty($company->profile_photo) && Storage::disk('public')->exists($company->profile_photo)) {
                Storage::disk('public')->delete($company->profile_photo);
            }

            $userUpdate['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $company->forceFill($userUpdate)->save();

        return redirect()
            ->route('company.profile.edit')
            ->with('toast_success', 'Company profile updated successfully.');
    }

    private function buildNavCounts(): array
    {
        $companyId = (int) Auth::id();

        $companyJobsQuery = Job::query()
            ->where('organizer_role', 'company')
            ->where('company_user_id', $companyId);

        $jobIds = (clone $companyJobsQuery)->pluck('id');

        return [
            'jobBadgeCount' => (clone $companyJobsQuery)->count(),
            'alumniBadgeCount' => User::where('role', 'alumni')->count(),
            'applicationBadgeCount' => JobApplication::whereIn('job_id', $jobIds)->count(),
            'workshopBadgeCount' => Workshop::where('company_user_id', $companyId)->count(),
        ];
    }
}
