<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function requestForm(Request $request)
    {
        return view('auth.forgot-password', [
            'role' => strtolower((string)$request->query('role', 'alumni')),
            'identifier' => (string)$request->query('identifier', ''),
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'in:alumni,college,company,admin'],
            'identifier' => ['required', 'string', 'max:255'],
        ]);

        $role = strtolower($data['role']);
        $identifier = trim($data['identifier']);

        // نفس منطق تسجيل الدخول عندك
        $field = ($role === 'alumni') ? 'academic_id' : 'email';

        $q = User::query()->where($field, $identifier);

        if ($role === 'admin') {
            $q->whereIn('role', ['admin', 'super_admin']);
        } else {
            $q->where('role', $role);
        }

        $user = $q->first();

        // ✅ لأسباب أمنية: نفس الرسالة سواء المستخدم موجود أو لا
        $genericMsg = 'If the account exists, a password reset link has been sent to the registered email.';

        // لو ما لقينا user أو ما عنده ايميل → نفس الرسالة
        if (!$user || empty($user->email)) {
            return back()->with('toast_success', $genericMsg)->withInput();
        }

        // ✅ Mailtrap will capture this email if SMTP configured correctly
        $status = Password::sendResetLink(['email' => $user->email]);

        // حتى لو فشل mail: ما نكشف تفاصيل
        if ($status !== Password::RESET_LINK_SENT) {
            return back()->with('toast_success', $genericMsg)->withInput();
        }

        return back()->with('toast_success', $genericMsg)->withInput();
    }

    public function showResetForm(string $token, Request $request)
    {
        $email = (string)$request->query('email', '');
        abort_if($email === '', 404);

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => $data['email'],
                'token' => $data['token'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'] ?? '',
            ],
            function ($user) use ($data) {
                // password cast عندك hashed => سيُشفّر تلقائياً
                $user->forceFill([
                    'password' => $data['password'],
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withErrors(['email' => 'This reset link is invalid or expired.'])
                ->withInput();
        }

        return redirect()->route('login')
            ->with('toast_success', 'Password updated successfully. Please sign in.');
    }
}
