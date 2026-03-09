<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SupportRequestController extends Controller
{
    public function show(Request $request)
    {
        return view('support.request', [
            'role' => strtolower((string)$request->query('role', 'alumni')),
            'identifier' => (string)$request->query('identifier', ''),

            // ✅ last saved tracking (30 days cookie) - device based
            'lastCode'  => (string) $request->cookie('support_last_code', ''),
            'lastEmail' => (string) $request->cookie('support_last_email', ''),
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('support_tickets')) {
            return back()->withErrors([
                'message' => 'Support system is not ready yet. Please try again later.',
            ])->withInput();
        }

        $data = $request->validate([
            'role' => ['required', 'in:alumni,college,company,admin'],
            'identifier' => ['required', 'string', 'max:255'],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],

            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $role = strtolower(trim($data['role']));
        $identifier = trim((string)$data['identifier']);

        // ✅ normalize email (IMPORTANT)
        $emailLower = strtolower(trim((string)$data['email']));

        // Best-effort linking to existing user
        $field = ($role === 'alumni') ? 'academic_id' : 'email';

        $userQuery = User::query()->where($field, $identifier);

        if ($role === 'admin') {
            $userQuery->whereIn('role', ['admin', 'super_admin']);
        } else {
            $userQuery->where('role', $role);
        }

        $user = $userQuery->first();

        // ✅ Realistic: Prevent duplicate open tickets for same user/device/email
        $existing = $this->findOpenTicket($user?->id, $emailLower, $role, $identifier);

        if ($existing) {
            $this->ensureTrackingCode($existing);

            return redirect()
                ->route('support.track.show', ['code' => $existing->tracking_code, 'email' => $emailLower])
                ->with('toast_success', 'You already have an open support request. Redirected to your latest ticket.')
                ->cookie($this->supportCookie('support_last_code', $existing->tracking_code))
                ->cookie($this->supportCookie('support_last_email', $emailLower));
        }

        $priority = 'medium';
        if ($user && (bool)($user->is_suspended ?? false)) $priority = 'high';

        $attrs = [
            'user_id'  => $user?->id,
            'name'     => $data['name'],
            'email'    => $emailLower,
            'message'  => $data['message'],
            'status'   => 'open',
            'priority' => $priority,
        ];

        if (Schema::hasColumn('support_tickets', 'role')) $attrs['role'] = $role;
        if (Schema::hasColumn('support_tickets', 'identifier')) $attrs['identifier'] = $identifier;

        if (Schema::hasColumn('support_tickets', 'title')) $attrs['title'] = $data['title'];
        if (Schema::hasColumn('support_tickets', 'subject')) $attrs['subject'] = $data['title'];

        if (Schema::hasColumn('support_tickets', 'admin_id')) $attrs['admin_id'] = null;
        if (Schema::hasColumn('support_tickets', 'admin_reply')) $attrs['admin_reply'] = null;
        if (Schema::hasColumn('support_tickets', 'resolved_at')) $attrs['resolved_at'] = null;

        // ✅ tracking_code
        if (Schema::hasColumn('support_tickets', 'tracking_code')) {
            $attrs['tracking_code'] = $this->generateTrackingCode();
        }

        $ticket = SupportTicket::create($attrs);

        // ✅ Safety: ensure code exists
        $this->ensureTrackingCode($ticket);

        // ✅ Notify admins (existing behavior)
        try {
            if (Schema::hasTable('notifications')) {
                $admins = User::whereIn('role', ['admin','super_admin'])->get();
                $url = "/admin/support?status=open#ticket-{$ticket->id}";

                foreach ($admins as $admin) {
                    $admin->notify(new SupportTicketCreatedNotification([
                        'title' => 'New Support Ticket',
                        'message' => "New ticket: {$data['title']} ({$data['name']})",
                        'action_url' => $url,
                        'icon' => 'help-circle',
                        'ticket_id' => $ticket->id,
                    ]));
                }
            }
        } catch (\Throwable $e) {}

        // ✅ Redirect to tracking page + save cookie (device-based)
        return redirect()
            ->route('support.track.show', ['code' => $ticket->tracking_code, 'email' => $emailLower])
            ->with('toast_success', "Request sent. Save your tracking code: {$ticket->tracking_code}")
            ->cookie($this->supportCookie('support_last_code', $ticket->tracking_code))
            ->cookie($this->supportCookie('support_last_email', $emailLower));
    }

    private function findOpenTicket(?int $userId, string $email, string $role, string $identifier): ?SupportTicket
    {
        $q = SupportTicket::query()
            ->whereIn('status', ['open','in_progress'])
            ->orderByDesc('id');

        if ($userId) {
            return $q->where('user_id', $userId)->first();
        }

        // fallback: match by email + role + identifier (if columns exist)
        $q->where('email', $email);

        if (Schema::hasColumn('support_tickets', 'role')) $q->where('role', $role);
        if (Schema::hasColumn('support_tickets', 'identifier')) $q->where('identifier', $identifier);

        return $q->first();
    }

    private function ensureTrackingCode(SupportTicket $ticket): void
    {
        try {
            if (!Schema::hasColumn('support_tickets', 'tracking_code')) return;
            if (!empty($ticket->tracking_code)) return;

            $ticket->forceFill(['tracking_code' => $this->generateTrackingCode()])->save();
        } catch (\Throwable $e) {}
    }

    private function generateTrackingCode(): string
    {
        do {
            $code = 'SUP-' . strtoupper(Str::random(8));
        } while (SupportTicket::where('tracking_code', $code)->exists());

        return $code;
    }

    private function supportCookie(string $key, string $value)
    {
        // 30 days
        return Cookie::make($key, $value, 60 * 24 * 30);
    }
}
