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

        // Find user by role (Admin accepts admin OR super_admin)
        $userQuery = User::query()->where($field, $identifier);

        if ($role === 'admin') {
            $userQuery->whereIn('role', ['admin', 'super_admin']);
        } else {
            $userQuery->where('role', $role);
        }

        $user = $userQuery->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['identifier' => 'Invalid credentials.'])
                ->withInput($request->only('role', 'identifier'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath($user->role));
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
