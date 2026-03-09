<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketRepliedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = SupportTicket::with(['user','admin'])->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tickets = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
        ];

        return view('admin.support', compact('tickets', 'counts', 'status'));
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status' => ['required','in:open,in_progress,resolved'],
            'priority' => ['required','in:low,medium,high'],
        ]);

        $ticket->update([
            'status' => $data['status'],
            'priority' => $data['priority'],
            'admin_id' => Auth::id(),
            'resolved_at' => $data['status'] === 'resolved' ? now() : null,
        ]);

        return back()->with('toast_success', 'Ticket updated successfully.');
    }

    public function respond(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status' => ['required','in:open,in_progress,resolved'],
            'priority' => ['required','in:low,medium,high'],
            'admin_reply' => ['nullable','string','max:5000'],
            'include_reset_link' => ['nullable'],
        ]);

        $oldReply = trim((string)($ticket->admin_reply ?? ''));
        $newReply = trim((string)($data['admin_reply'] ?? ''));


        $this->ensureTrackingCode($ticket);


        if ($request->boolean('include_reset_link')) {
            $resetUrl = $this->makePasswordResetUrl($ticket);

            if ($resetUrl) {
                $expire = (int) (config('auth.passwords.users.expire') ?? 60);

                if ($newReply === '') {
                    $newReply =
                        "Please use the following link to reset your password:\n" .
                        $resetUrl . "\n\n" .
                        "This link will expire in {$expire} minutes.";
                } else {
                    $newReply .= "\n\nPassword reset link:\n{$resetUrl}\n(Expires in {$expire} minutes)";
                }
            } else {
                if ($newReply === '') {
                    $newReply = "I couldn’t generate a password reset link because the account email is not available.";
                }
            }
        }

        $update = [
            'status' => $data['status'],
            'priority' => $data['priority'],
            'admin_id' => Auth::id(),
            'resolved_at' => $data['status'] === 'resolved' ? now() : null,
        ];

        if ($newReply !== '') {
            $update['admin_reply'] = $newReply;

            if (Schema::hasColumn('support_tickets', 'admin_replied_at')) {
                $update['admin_replied_at'] = now();
            }
        }

        $ticket->update($update);


        if ($newReply !== '' && $newReply !== $oldReply) {
            $this->notifyOwner($ticket, $newReply);
        }

        return back()->with('toast_success', 'Reply saved successfully.');
    }


    public function reply(Request $request, SupportTicket $ticket)
    {
        return $this->respond($request, $ticket);
    }

    private function notifyOwner(SupportTicket $ticket, string $reply): void
    {
        try {
            if (!Schema::hasTable('notifications')) return;

            $owner = $ticket->user;
            if (!$owner) return;


            $actionUrl = null;
            if (!empty($ticket->tracking_code)) {
                $params = ['code' => $ticket->tracking_code];
                if (!empty($ticket->email)) $params['email'] = $ticket->email;
                $actionUrl = route('support.track.show', $params);
            }

            $ticketTitle = $ticket->title ?? $ticket->subject ?? "Ticket #{$ticket->id}";

            $payload = [
                'kind' => 'support_ticket_reply',
                'title' => 'Support replied to your ticket',
                'message' => "Reply on: {$ticketTitle}",
                'body' => Str::limit($reply, 140),
                'icon' => 'message-square',
                'ticket_id' => $ticket->id,
                'status' => $ticket->status,
                'action_url' => $actionUrl,
                'created_at' => now()->toISOString(),
            ];

            $owner->notify(new SupportTicketRepliedNotification($payload));
        } catch (\Throwable $e) {
            return;
        }
    }

    private function ensureTrackingCode(SupportTicket $ticket): void
    {
        try {
            if (!Schema::hasColumn('support_tickets', 'tracking_code')) return;
            if (!empty($ticket->tracking_code)) return;

            do {
                $code = 'SUP-' . strtoupper(Str::random(8));
            } while (SupportTicket::where('tracking_code', $code)->exists());

            $ticket->forceFill(['tracking_code' => $code])->save();
        } catch (\Throwable $e) {
            return;
        }
    }



    private function makePasswordResetUrl(SupportTicket $ticket): ?string
    {
        try {

        $user = $ticket->user;

            if (!$user && !empty($ticket->email)) {
                $user = User::where('email', $ticket->email)->first();
            }

            if (!$user) return null;

            $email = trim((string)($user->email ?? ''));
            if ($email === '') return null;

            // ✅ This will work after User implements CanResetPassword
            $token = Password::broker()->createToken($user);

            // route: /reset-password/{token}?email=...
            return route('password.reset', [
                'token' => $token,
                'email' => $email,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
