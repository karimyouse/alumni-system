<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ✅ Only show tickets that belong to the logged-in user
        $tickets = SupportTicket::query()
            ->with(['admin']) // optional (if you show admin name)
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('support.tickets.index', compact('tickets'));
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();

        // ✅ Hard authorization: user can only view their own ticket
        abort_unless((int)$ticket->user_id === (int)$user->id, 403);

        $ticket->loadMissing(['admin']);

        return view('support.tickets.show', compact('ticket'));
    }
}
