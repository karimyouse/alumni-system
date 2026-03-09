<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function read(Request $request, string $id)
    {
        $user = $request->user();

        $n = $user->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();

        return $this->safeRedirect($request, $request->input('redirect_to'));
    }

    public function readAll(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return $this->safeRedirect($request, $request->input('redirect_to'));
    }

    private function safeRedirect(Request $request, ?string $to)
    {
        $target = $this->normalizeInternal($request, $to);
        if ($target) {
            return redirect()->to($target);
        }
        return back();
    }

    /**
     * ✅ يقبل:
     * - /path?x=1#frag
     * - أو absolute URL لكن فقط لو نفس host/port (حتى لو APP_URL مختلف)
     */
    private function normalizeInternal(Request $request, ?string $u): ?string
    {
        if (!is_string($u)) return null;

        $u = trim($u);
        if ($u === '') return null;

        // relative internal path
        if (str_starts_with($u, '/')) {
            return $u;
        }

        // absolute url => accept only if same host/port
        $p = parse_url($u);
        if (!$p || empty($p['host'])) return null;

        $host = (string)($p['host'] ?? '');
        $port = isset($p['port']) ? (int)$p['port'] : null;

        $reqHost = (string)$request->getHost();
        $reqPort = (int)$request->getPort();

        if ($host !== $reqHost) return null;
        if ($port !== null && $port !== $reqPort) return null;

        $path = $p['path'] ?? '/';
        $query = isset($p['query']) ? '?'.$p['query'] : '';
        $frag  = isset($p['fragment']) ? '#'.$p['fragment'] : '';

        return $path.$query.$frag;
    }
}
