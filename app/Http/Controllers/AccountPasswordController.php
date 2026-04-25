<?php

namespace App\Http\Controllers;

use App\Support\SessionSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AccountPasswordController extends Controller
{
    public function update(Request $request, SessionSecurity $sessionSecurity)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => __('The current password is incorrect.')])
                ->withInput();
        }

        $update = [
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ];

        if (Schema::hasColumn('users', 'password_changed_at')) {
            $update['password_changed_at'] = now();
        }

        $user->forceFill($update)->save();
        $request->session()->regenerate();
        $sessionSecurity->invalidateAllSessionsFor($user, $sessionSecurity->currentSessionId($request));

        return back()->with('toast_success', __('session.password_changed_others'));
    }
}
