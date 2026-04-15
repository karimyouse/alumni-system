<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('admin.profile', [
            'user' => Auth::user(),
            'totalUsers' => User::count(),
            'openSupportTickets' => DB::getSchemaBuilder()->hasTable('support_tickets')
                ? DB::table('support_tickets')->whereIn('status', ['open', 'in_progress'])->count()
                : 0,
            'pendingCompanies' => DB::getSchemaBuilder()->hasTable('company_profiles')
                ? DB::table('company_profiles')->where('status', 'pending')->count()
                : 0,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();
        $update = [
            'name' => $data['name'],
        ];

        if ($request->hasFile('profile_photo')) {
            if (!empty($user->profile_photo) && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $update['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($update);

        return redirect()
            ->route('admin.profile')
            ->with('toast_success', 'Admin profile updated successfully.');
    }
}
