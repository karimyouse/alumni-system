<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Recommendation;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $userId = (int) $user->id;

        $profile = AlumniProfile::firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        $navCounts = $this->buildNavCounts($userId);

        return view('alumni.profile', array_merge([
            'user' => $user,
            'profile' => $profile,
        ], $navCounts));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $userId = (int) $user->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:120'],
            'major' => ['nullable', 'string', 'max:120'],
            'graduation_year' => ['nullable', 'string', 'max:10'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'portfolio' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->update([
            'name' => $data['name'],
        ]);

        $profile = AlumniProfile::firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        $updateData = [
            'phone' => $data['phone'] ?? null,
            'location' => $data['location'] ?? null,
            'major' => $data['major'] ?? null,
            'graduation_year' => $data['graduation_year'] ?? null,
            'gpa' => $data['gpa'] ?? null,
            'bio' => $data['bio'] ?? null,
            'skills' => $data['skills'] ?? null,
            'linkedin' => $data['linkedin'] ?? null,
            'portfolio' => $data['portfolio'] ?? null,
        ];

        if ($request->hasFile('profile_photo')) {
            if (!empty($profile->profile_photo) && Storage::disk('public')->exists($profile->profile_photo)) {
                Storage::disk('public')->delete($profile->profile_photo);
            }

            $updateData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $profile->update($updateData);

        return redirect()
            ->route('alumni.profile')
            ->with('toast_success', 'Profile updated successfully!');
    }

    private function buildNavCounts(int $userId): array
    {
        $jobsQuery = Job::query()->orderByDesc('id');

        if (Schema::hasColumn('jobs', 'approval_status')) {
            $jobsQuery->where('approval_status', 'approved');
        }

        if (Schema::hasColumn('jobs', 'status')) {
            $jobsQuery->where('status', 'active');
        }

        $jobBadgeCount = (clone $jobsQuery)->count();

        $workshopsQuery = Workshop::query()->orderByDesc('id');

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $workshopsQuery->where('proposal_status', 'approved');
        }

        if (Schema::hasColumn('workshops', 'status')) {
            $workshopsQuery->where('status', 'upcoming');
        }

        $workshopBadgeCount = (clone $workshopsQuery)->count();

        $scholarshipBadgeCount = Scholarship::query()->count();

        $recommendationsReceived = Recommendation::where('to_user_id', $userId)->count();

        $jobApplicationsCount = JobApplication::where('alumni_user_id', $userId)->count();
        $scholarshipApplicationsCount = ScholarshipApplication::where('alumni_user_id', $userId)->count();

        $workshopApplicationsQuery = WorkshopRegistration::where('alumni_user_id', $userId);
        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $workshopApplicationsQuery->where('status', 'registered');
        }
        $workshopApplicationsCount = $workshopApplicationsQuery->count();

        $applicationsBadgeCount =
            $jobApplicationsCount +
            $scholarshipApplicationsCount +
            $workshopApplicationsCount;

        return [
            'jobBadgeCount' => $jobBadgeCount,
            'workshopBadgeCount' => $workshopBadgeCount,
            'scholarshipBadgeCount' => $scholarshipBadgeCount,
            'recommendationsReceived' => $recommendationsReceived,
            'applicationsBadgeCount' => $applicationsBadgeCount,
        ];
    }
}
