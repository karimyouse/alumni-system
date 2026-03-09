<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();


        $tickets = SupportTicket::query()
            ->with(['admin'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('support.tickets.index', compact('tickets'));
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();


        abort_unless((int)$ticket->user_id === (int)$user->id, 403);

        $ticket->loadMissing(['admin']);

        return view('support.tickets.show', compact('ticket'));
    }
}
