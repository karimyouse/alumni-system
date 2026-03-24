<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    private function hexToHsl(string $hex): string
    {
        $hex = ltrim(trim($hex), '#');

        if (strlen($hex) === 3) {
            $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
        }

        if (strlen($hex) !== 6) {
            return '217 91% 60%';
        }

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = 0;
            $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            if ($max === $r) {
                $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
            } elseif ($max === $g) {
                $h = ($b - $r) / $d + 2;
            } else {
                $h = ($r - $g) / $d + 4;
            }

            $h /= 6;
        }

        $hDeg = round($h * 360);
        $sPct = round($s * 100);
        $lPct = round($l * 100);

        return "{$hDeg} {$sPct}% {$lPct}%";
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $fallbackSettings = (object) [
                'institution_name' => 'Alumni System',
                'primary_color' => '#2563eb',
            ];

            $fallbackTheme = [
                'primary_hsl' => '217 91% 60%',
                'primary_hex' => '#2563eb',
            ];

            try {
                if (!Schema::hasTable('system_settings')) {
                    $settings = $fallbackSettings;
                } else {
                    $settings = Cache::remember('system_settings_v1', 60, function () {
                        return SystemSetting::query()->first();
                    }) ?: $fallbackSettings;
                }

                $primaryHex = $settings->primary_color ?? '#2563eb';
                $primaryHsl = $this->hexToHsl($primaryHex);

                $view->with('appSettings', $settings)
                    ->with('appTheme', [
                        'primary_hsl' => $primaryHsl,
                        'primary_hex' => $primaryHex,
                    ]);
            } catch (\Throwable $e) {
                $view->with('appSettings', $fallbackSettings)
                    ->with('appTheme', $fallbackTheme);
            }

            $navUnreadCount = 0;
            $navNotifications = collect([]);

            try {
                if (Auth::check() && Schema::hasTable('notifications')) {
                    $u = Auth::user();

                    $navUnreadCount = $u->unreadNotifications()->count();

                    $navNotifications = $u->notifications()
                        ->latest()
                        ->limit(8)
                        ->get();
                }
            } catch (\Throwable $e) {
                //
            }

            $view->with('navUnreadCount', $navUnreadCount)
                ->with('navNotifications', $navNotifications);
        });
    }
}
