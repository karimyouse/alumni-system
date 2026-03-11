<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $profile = AlumniProfile::firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        return view('alumni.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'location' => ['nullable','string','max:120'],
            'major' => ['nullable','string','max:120'],
            'graduation_year' => ['nullable','string','max:10'],
            'gpa' => ['nullable','numeric','min:0','max:4'],
            'bio' => ['nullable','string','max:1000'],
            'skills' => ['nullable','string','max:500'],
            'linkedin' => ['nullable','string','max:255'],
            'portfolio' => ['nullable','string','max:255'],
        ]);

        $user->update([
            'name' => $data['name'],
        ]);

        AlumniProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $data['phone'] ?? null,
                'location' => $data['location'] ?? null,
                'major' => $data['major'] ?? null,
                'graduation_year' => $data['graduation_year'] ?? null,
                'gpa' => $data['gpa'] ?? null,
                'bio' => $data['bio'] ?? null,
                'skills' => $data['skills'] ?? null,
                'linkedin' => $data['linkedin'] ?? null,
                'portfolio' => $data['portfolio'] ?? null,
            ]
        );

        return back()->with('toast_success', __('Profile updated successfully!'));
    }
}
