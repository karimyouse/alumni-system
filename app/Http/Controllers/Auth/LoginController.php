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
            'role' => ['required', 'in:alumni,college,company,admin'],
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // React role "Super Admin" => نخزنها super_admin في DB
        $role = $data['role'] === 'admin' ? 'super_admin' : $data['role'];

        $query = User::query()->where('role', $role);

        if ($role === 'alumni') {
            $query->where('academic_id', $data['identifier']);
        } else {
            $query->where('email', $data['identifier']);
        }

        $user = $query->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['identifier' => 'Invalid credentials'])->withInput();
        }

        Auth::login($user, true);

        return redirect(match ($user->role) {
    'alumni' => '/alumni',
    'college' => '/college',
    'company' => '/company',
    'super_admin' => '/admin',
    default => '/',
    })->with('toast_success', 'Successfully logged in!');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
