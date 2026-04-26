<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SessionSecurity
{
    public function supportsDatabaseSessions(): bool
    {
        return Schema::hasTable($this->sessionTable());
    }

    public function currentSessionId(?Request $request = null): ?string
    {
        $sessionId = $request?->session()?->getId() ?? session()->getId();

        return is_string($sessionId) && $sessionId !== '' ? $sessionId : null;
    }

    public function invalidateAllSessionsFor(User $user, ?string $exceptSessionId = null): int
    {
        if (!$this->supportsDatabaseSessions()) {
            return 0;
        }

        $query = DB::table($this->sessionTable())
            ->where('user_id', $user->getAuthIdentifier());

        if (is_string($exceptSessionId) && $exceptSessionId !== '') {
            $query->where('id', '!=', $exceptSessionId);
        }

        return $query->delete();
    }

    public function activeSessionsFor(User $user, ?string $currentSessionId = null): Collection
    {
        if (!$this->supportsDatabaseSessions()) {
            return collect();
        }

        return DB::table($this->sessionTable())
            ->where('user_id', $user->getAuthIdentifier())
            ->orderByDesc('last_activity')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->map(fn (object $session) => $this->mapSession($session, $currentSessionId));
    }

    public function removeSessionFor(User $user, string $sessionId): bool
    {
        if (!$this->supportsDatabaseSessions()) {
            return false;
        }

        return DB::table($this->sessionTable())
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', $sessionId)
            ->delete() > 0;
    }

    private function mapSession(object $session, ?string $currentSessionId): array
    {
        $userAgent = trim((string) ($session->user_agent ?? ''));
        $platform = $this->platformFromUserAgent($userAgent);
        $browser = $this->browserFromUserAgent($userAgent);
        $deviceType = $this->deviceTypeFromUserAgent($userAgent);
        $lastActivity = Carbon::createFromTimestamp((int) ($session->last_activity ?: now()->timestamp));

        $label = collect([$platform, $browser, $deviceType])
            ->filter(fn (?string $value) => filled($value) && !str_starts_with($value, 'Unknown'))
            ->unique()
            ->implode(' / ');

        return [
            'id' => (string) $session->id,
            'is_current' => (string) $session->id === (string) $currentSessionId,
            'can_delete' => (string) $session->id !== (string) $currentSessionId,
            'label' => $label !== '' ? $label : 'Unknown device',
            'platform' => $platform,
            'browser' => $browser,
            'device_type' => $deviceType,
            'ip_address' => $session->ip_address ?: '-',
            'user_agent' => Str::limit($userAgent !== '' ? $userAgent : 'Unknown user agent', 180),
            'last_activity_at' => $lastActivity,
            'last_activity_human' => $lastActivity->diffForHumans(),
        ];
    }

    private function browserFromUserAgent(string $userAgent): string
    {
        $browserPatterns = [
            'Edge' => ['Edg/', 'Edge/'],
            'Opera' => ['OPR/', 'Opera/'],
            'Chrome' => ['Chrome/'],
            'Firefox' => ['Firefox/'],
            'Safari' => ['Safari/'],
            'Internet Explorer' => ['MSIE ', 'Trident/'],
        ];

        foreach ($browserPatterns as $browser => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($userAgent, $needle)) {
                    if ($browser === 'Safari' && str_contains($userAgent, 'Chrome/')) {
                        continue;
                    }

                    return $browser;
                }
            }
        }

        return 'Unknown Browser';
    }

    private function platformFromUserAgent(string $userAgent): string
    {
        $platformPatterns = [
            'Windows' => ['Windows'],
            'macOS' => ['Macintosh', 'Mac OS X'],
            'iPhone' => ['iPhone'],
            'iPad' => ['iPad'],
            'Android' => ['Android'],
            'Linux' => ['Linux'],
            'Chrome OS' => ['CrOS'],
        ];

        foreach ($platformPatterns as $platform => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($userAgent, $needle)) {
                    return $platform;
                }
            }
        }

        return 'Unknown Platform';
    }

    private function deviceTypeFromUserAgent(string $userAgent): string
    {
        if (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
            return 'Tablet';
        }

        if (
            str_contains($userAgent, 'Mobile') ||
            str_contains($userAgent, 'Android') ||
            str_contains($userAgent, 'iPhone')
        ) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private function sessionTable(): string
    {
        return (string) config('session.table', 'sessions');
    }
}
