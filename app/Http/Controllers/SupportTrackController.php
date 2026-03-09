<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTrackController extends Controller
{
    public function show(Request $request)
    {
        $code  = trim((string)$request->query('code', ''));
        $email = strtolower(trim((string)$request->query('email', '')));

        // Always pass back what user typed (for sticky inputs)
        $viewData = [
            'code' => $code,
            'email' => $email,
            'ticket' => null,
            'notFound' => false,
        ];

        // If user didn't submit yet, just show the form
        if ($code === '' && $email === '') {
            return view('support.track', $viewData);
        }

        // If only one of them provided -> treat as not found (still safe)
        if ($code === '' || $email === '') {
            $viewData['notFound'] = true;
            return view('support.track', $viewData);
        }

        // ✅ Secure lookup: must match tracking_code + email
        $ticket = SupportTicket::query()
            ->where('tracking_code', $code)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$ticket) {
            $viewData['notFound'] = true;
            return view('support.track', $viewData);
        }

        $viewData['ticket'] = $ticket;

        return view('support.track', $viewData);
    }
}
