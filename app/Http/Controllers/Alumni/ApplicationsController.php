<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\ScholarshipApplication;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\Auth;

class ApplicationsController extends Controller
{
    private function mapStatus(string $raw, string $type): array
    {
        $raw = strtolower(trim($raw));

        if ($type === 'workshops') {
            if ($raw === 'registered') {
                return [__('Accepted'), 'bg-green-500/15 text-green-400'];
            }
            if ($raw === 'cancelled') {
                return [__('Rejected'), 'bg-red-500/15 text-red-400'];
            }
            return [__(ucfirst($raw)), 'bg-secondary text-secondary-foreground'];
        }

        return match ($raw) {
            'pending'  => [__('Pending'), 'bg-muted text-foreground'],
            'reviewed' => [__('Under Review'), 'bg-blue-500/15 text-blue-400'],
            'accepted' => [__('Accepted'), 'bg-green-500/15 text-green-400'],
            'rejected' => [__('Rejected'), 'bg-red-500/15 text-red-400'],
            default    => [__(ucfirst($raw)), 'bg-secondary text-secondary-foreground'],
        };
    }

    public function index()
    {
        $userId = Auth::id();

        $jobApps = JobApplication::with('job')
            ->where('alumni_user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($a) {
                [$label, $class] = $this->mapStatus($a->status ?? 'pending', 'jobs');
                return [
                    'id' => $a->id,
                    'type' => 'jobs',
                    'title' => $a->job?->title ?? __('Job'),
                    'org' => $a->job?->company_name ?? __('Company'),
                    'date_text' => __('Applied') . ' ' . ($a->applied_date ?: ($a->created_at?->format('M d, Y') ?? '')),
                    'status_label' => $label,
                    'status_class' => $class,
                    'icon' => 'briefcase',
                ];
            })->values();

        $schApps = ScholarshipApplication::with('scholarship')
            ->where('alumni_user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($a) {
                [$label, $class] = $this->mapStatus($a->status ?? 'pending', 'scholarships');
                return [
                    'id' => $a->id,
                    'type' => 'scholarships',
                    'title' => $a->scholarship?->title ?? __('Scholarship'),
                    'org' => 'PTC',
                    'date_text' => __('Applied') . ' ' . ($a->applied_date ?: ($a->created_at?->format('M d, Y') ?? '')),
                    'status_label' => $label,
                    'status_class' => $class,
                    'icon' => 'graduation-cap',
                ];
            })->values();

        $wsRegs = WorkshopRegistration::with('workshop')
            ->where('alumni_user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($r) {
                [$label, $class] = $this->mapStatus($r->status ?? 'registered', 'workshops');
                return [
                    'id' => $r->id,
                    'type' => 'workshops',
                    'title' => $r->workshop?->title ?? __('Workshop'),
                    'org' => 'PTC',
                    'date_text' => __('Applied') . ' ' . ($r->created_at?->format('M d, Y') ?? ''),
                    'status_label' => $label,
                    'status_class' => $class,
                    'icon' => 'calendar-days',
                ];
            })->values();

        $all = $jobApps->concat($schApps)->concat($wsRegs)->values();

        $tabs = [
            ['key' => 'all', 'label' => __('All'), 'count' => $all->count()],
            ['key' => 'jobs', 'label' => __('Jobs'), 'count' => $jobApps->count()],
            ['key' => 'scholarships', 'label' => __('Scholarships'), 'count' => $schApps->count()],
            ['key' => 'workshops', 'label' => __('Workshops'), 'count' => $wsRegs->count()],
        ];

        $itemsByTab = [
            'all' => $all,
            'jobs' => $jobApps,
            'scholarships' => $schApps,
            'workshops' => $wsRegs,
        ];

        return view('alumni.applications', compact('tabs', 'itemsByTab'));
    }

    public function show(string $type, int $id)
    {
        $userId = Auth::id();

        if ($type === 'jobs') {
            $app = JobApplication::with('job')
                ->where('alumni_user_id', $userId)
                ->findOrFail($id);

            return view('alumni.application-show', [
                'type' => 'jobs',
                'app' => $app,
                'title' => 'Job Application Details',
            ]);
        }

        if ($type === 'scholarships') {
            $app = ScholarshipApplication::with('scholarship')
                ->where('alumni_user_id', $userId)
                ->findOrFail($id);

            return view('alumni.application-show', [
                'type' => 'scholarships',
                'app' => $app,
                'title' => 'Scholarship Application Details',
            ]);
        }

        $app = WorkshopRegistration::with('workshop')
            ->where('alumni_user_id', $userId)
            ->findOrFail($id);

        return view('alumni.application-show', [
            'type' => 'workshops',
            'app' => $app,
            'title' => 'Workshop Registration Details',
        ]);
    }

    public function withdraw(string $type, int $id)
    {
        $userId = Auth::id();

        if ($type === 'jobs') {
            $app = JobApplication::where('alumni_user_id', $userId)->findOrFail($id);

            if (!in_array($app->status, ['pending', 'reviewed'])) {
                return back()->with('toast_success', __('You cannot withdraw this application now.'));
            }

            $app->delete();

            return redirect()
                ->route('alumni.applications')
                ->with('toast_success', __('Application withdrawn successfully.'));
        }

        if ($type === 'scholarships') {
            $app = ScholarshipApplication::where('alumni_user_id', $userId)->findOrFail($id);

            if (!in_array($app->status, ['pending', 'reviewed'])) {
                return back()->with('toast_success', __('You cannot withdraw this application now.'));
            }

            $app->delete();

            return redirect()
                ->route('alumni.applications')
                ->with('toast_success', __('Scholarship application withdrawn.'));
        }

        $app = WorkshopRegistration::with('workshop')
            ->where('alumni_user_id', $userId)
            ->findOrFail($id);

        if ($app->workshop && $app->status === 'registered') {
            $app->workshop->increment('spots');
        }

        $app->delete();

        return redirect()
            ->route('alumni.applications')
            ->with('toast_success', __('Workshop registration cancelled.'));
    }
}
