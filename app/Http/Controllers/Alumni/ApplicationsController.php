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

        // workshop registrations
        if ($type === 'workshops') {
            if ($raw === 'registered') {
                return ['Accepted', 'bg-green-500/15 text-green-400'];
            }
            if ($raw === 'cancelled') {
                return ['Rejected', 'bg-red-500/15 text-red-400'];
            }
            return [ucfirst($raw), 'bg-secondary text-secondary-foreground'];
        }

        // jobs & scholarships
        return match ($raw) {
            'pending'  => ['Pending', 'bg-muted text-foreground'],
            'reviewed' => ['Under Review', 'bg-blue-500/15 text-blue-400'],
            'accepted' => ['Accepted', 'bg-green-500/15 text-green-400'],
            'rejected' => ['Rejected', 'bg-red-500/15 text-red-400'],
            default    => [ucfirst($raw), 'bg-secondary text-secondary-foreground'],
        };
    }

    public function index()
    {


        $userId = Auth::id();

        // Jobs applications
        $jobApps = JobApplication::with('job')
            ->where('alumni_user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($a) {
                [$label, $class] = $this->mapStatus($a->status ?? 'pending', 'jobs');
                return [
                    'id' => $a->id,
                    'type' => 'jobs',
                    'title' => $a->job?->title ?? 'Job',
                    'org' => $a->job?->company_name ?? 'Company',
                    'date_text' => 'Applied ' . ($a->applied_date ?: ($a->created_at?->format('M d, Y') ?? '')),
                    'status_label' => $label,
                    'status_class' => $class,
                    'icon' => 'briefcase',
                ];
            })->values();

        // Scholarships applications
        $schApps = ScholarshipApplication::with('scholarship')
            ->where('alumni_user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($a) {
                [$label, $class] = $this->mapStatus($a->status ?? 'pending', 'scholarships');
                return [
                    'id' => $a->id,
                    'type' => 'scholarships',
                    'title' => $a->scholarship?->title ?? 'Scholarship',
                    'org' => 'PTC',
                    'date_text' => 'Applied ' . ($a->applied_date ?: ($a->created_at?->format('M d, Y') ?? '')),
                    'status_label' => $label,
                    'status_class' => $class,
                    'icon' => 'graduation-cap',
                ];
            })->values();

        // Workshops registrations
        $wsRegs = WorkshopRegistration::with('workshop')
            ->where('alumni_user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($r) {
                [$label, $class] = $this->mapStatus($r->status ?? 'registered', 'workshops');
                return [
                    'id' => $r->id,
                    'type' => 'workshops',
                    'title' => $r->workshop?->title ?? 'Workshop',
                    'org' => 'PTC',
                    'date_text' => 'Applied ' . ($r->created_at?->format('M d, Y') ?? ''),
                    'status_label' => $label,
                    'status_class' => $class,
                    'icon' => 'calendar-days',
                ];
            })->values();

        // All combined (newest first by "id" per type order — good enough for demo)
        $all = $jobApps->concat($schApps)->concat($wsRegs)->values();

        // Counts
        $tabs = [
            ['key' => 'all', 'label' => 'All', 'count' => $all->count()],
            ['key' => 'jobs', 'label' => 'Jobs', 'count' => $jobApps->count()],
            ['key' => 'scholarships', 'label' => 'Scholarships', 'count' => $schApps->count()],
            ['key' => 'workshops', 'label' => 'Workshops', 'count' => $wsRegs->count()],
        ];

        $itemsByTab = [
            'all' => $all,
            'jobs' => $jobApps,
            'scholarships' => $schApps,
            'workshops' => $wsRegs,
        ];

        return view('alumni.applications', compact('tabs', 'itemsByTab'));
    }

        // ✅ View Details (real)
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

        // workshops
        $app = WorkshopRegistration::with('workshop')
            ->where('alumni_user_id', $userId)
            ->findOrFail($id);

        return view('alumni.application-show', [
            'type' => 'workshops',
            'app' => $app,
            'title' => 'Workshop Registration Details',
        ]);
    }

    // ✅ Withdraw / Cancel (real)
    public function withdraw(string $type, int $id)
    {
        $userId = Auth::id();

        if ($type === 'jobs') {
            $app = JobApplication::where('alumni_user_id', $userId)->findOrFail($id);

            // allow withdraw only if pending/reviewed
            if (!in_array($app->status, ['pending', 'reviewed'])) {
                return back()->with('toast_success', 'You cannot withdraw this application now.');
            }

            $app->delete();

            return redirect()
                ->route('alumni.applications')
                ->with('toast_success', 'Application withdrawn successfully.');
        }

        if ($type === 'scholarships') {
            $app = ScholarshipApplication::where('alumni_user_id', $userId)->findOrFail($id);

            // allow withdraw only if pending/reviewed
            if (!in_array($app->status, ['pending', 'reviewed'])) {
                return back()->with('toast_success', 'You cannot withdraw this application now.');
            }

            $app->delete();

            return redirect()
                ->route('alumni.applications')
                ->with('toast_success', 'Scholarship application withdrawn.');
        }

        // workshops = Cancel registration + return spot
        $app = WorkshopRegistration::with('workshop')
            ->where('alumni_user_id', $userId)
            ->findOrFail($id);

        if ($app->workshop && $app->status === 'registered') {
            $app->workshop->increment('spots');
        }

        $app->delete();

        return redirect()
            ->route('alumni.applications')
            ->with('toast_success', 'Workshop registration cancelled.');
    }

}
