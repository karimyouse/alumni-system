<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'string'],
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $role = strtolower(trim($data['role']));
        $identifier = trim($data['identifier']);
        $password = $data['password'];

        // Identify field based on role
        $field = ($role === 'alumni') ? 'academic_id' : 'email';

        $userQuery = User::query()->where($field, $identifier);

        // Admin accepts admin OR super_admin
        if ($role === 'admin') {
            $userQuery->whereIn('role', ['admin', 'super_admin']);
        } else {
            $userQuery->where('role', $role);
        }

        $user = $userQuery->first();

        // ❌ Invalid credentials
        if (!$user || !Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['identifier' => 'Invalid credentials. Please check your details and try again.'])
                ->with('login_blocked', 'invalid')
                ->withInput($request->only('role', 'identifier'));
        }

        // 🚫 Suspended user
        if ((bool)($user->is_suspended ?? false) === true) {
            return back()
                ->withErrors([
                    'identifier' => 'Your account has been suspended by the system administrator. Please contact support to request reactivation.'
                ])
                ->with('login_blocked', 'suspended')
                ->withInput($request->only('role', 'identifier'));
        }

        // ✅ Login
        Auth::login($user);
        $request->session()->regenerate();

        // Track last login time (safe)
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()
            ->intended($this->redirectPath($user->role))
            ->with('toast_success', 'Successfully logged in!');
    }

    private function redirectPath(string $role): string
    {
        $role = strtolower(trim($role));

        return match ($role) {
            'alumni' => '/alumni',
            'college' => '/college',
            'company' => '/company',
            'admin', 'super_admin' => '/admin',
            default => '/',
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
