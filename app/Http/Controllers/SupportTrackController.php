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


        $viewData = [
            'code' => $code,
            'email' => $email,
            'ticket' => null,
            'notFound' => false,
        ];


        if ($code === '' && $email === '') {
            return view('support.track', $viewData);
        }


        if ($code === '' || $email === '') {
            $viewData['notFound'] = true;
            return view('support.track', $viewData);
        }

        
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
