<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountSuspended;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'alumni');
        $q   = trim((string)$request->query('q', ''));

        $role = match ($tab) {
            'college' => 'college',
            'companies' => 'company',
            default => 'alumni',
        };

        $baseQuery = User::query()
            ->with('alumniProfile')
            ->where('role', $role)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('academic_id', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id');

        $users = $baseQuery->paginate(10)->withQueryString();

        $users->getCollection()->transform(function (User $user) {
            $user->display_initials = $this->initials($user->name);
            $user->display_photo_url = $this->profilePhotoUrl($user);

            return $user;
        });

        $counts = [
            'alumni' => User::where('role', 'alumni')->count(),
            'college' => User::where('role', 'college')->count(),
            'companies' => User::where('role', 'company')->count(),
        ];

        return view('admin.users', compact('users', 'counts', 'tab', 'q'));
    }

    public function show(User $user)
    {
        $user->load('alumniProfile');
        $user->display_initials = $this->initials($user->name);
        $user->display_photo_url = $this->profilePhotoUrl($user);

        return view('admin.user-show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:alumni,company,college,admin,super_admin'],
        ]);

        $user->update(['role' => $data['role']]);

        return back()->with('toast_success', 'Role updated.');
    }

    public function toggleSuspend(User $user)
    {
        $newState = !$user->is_suspended;

        $user->update([
            'is_suspended' => $newState,
        ]);


       
        $user->notify(new AccountSuspended($newState));

        return back()->with('toast_success', $newState ? 'User suspended.' : 'User activated.');
    }

    private function profilePhotoUrl(User $user): ?string
    {
        $photoPath = $user->role === 'alumni'
            ? ($user->alumniProfile?->profile_photo ?? null)
            : ($user->profile_photo ?? null);

        return $photoPath ? asset('storage/' . ltrim($photoPath, '/')) : null;
    }

    private function initials(?string $name): string
    {
        return collect(explode(' ', (string) $name))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(3)
            ->join('') ?: 'U';
    }
}
