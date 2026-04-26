<?php

namespace App\Http\Controllers;

use App\Support\SessionSecurity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AccountSessionController extends Controller
{
    public function updatePreference(Request $request, SessionSecurity $sessionSecurity): RedirectResponse
    {
        if (!Schema::hasColumn('users', 'allow_multiple_sessions')) {
            return back()->with('toast_error', __('session.migration_missing'));
        }

        $user = $request->user();
        $allowMultipleSessions = $request->boolean('allow_multiple_sessions');

        $user->forceFill([
            'allow_multiple_sessions' => $allowMultipleSessions,
        ])->save();

        if (!$allowMultipleSessions) {
            $sessionSecurity->invalidateAllSessionsFor($user, $sessionSecurity->currentSessionId($request));
        }

        return back()->with(
            'toast_success',
            $allowMultipleSessions
                ? __('session.multiple_enabled_toast')
                : __('session.single_enabled_toast')
        );
    }

    public function logoutOtherDevices(Request $request, SessionSecurity $sessionSecurity): RedirectResponse
    {
        $sessionSecurity->invalidateAllSessionsFor(
            $request->user(),
            $sessionSecurity->currentSessionId($request)
        );

        return back()->with('toast_success', __('session.logout_others_toast'));
    }

    public function logoutAllDevices(Request $request, SessionSecurity $sessionSecurity): RedirectResponse
    {
        $sessionSecurity->invalidateAllSessionsFor($request->user());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('toast_success', __('session.logout_all_toast'));
    }

    public function removeDevice(Request $request, string $sessionId, SessionSecurity $sessionSecurity): RedirectResponse
    {
        if ($sessionId === $sessionSecurity->currentSessionId($request)) {
            return back()->with('toast_error', __('session.remove_current_blocked'));
        }

        $removed = $sessionSecurity->removeSessionFor($request->user(), $sessionId);

        return back()->with(
            $removed ? 'toast_success' : 'toast_error',
            $removed ? __('session.remove_device_toast') : __('session.remove_missing')
        );
    }
}
