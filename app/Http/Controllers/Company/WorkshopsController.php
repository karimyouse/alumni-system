<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkshopsController extends Controller
{
    private function computeWorkshopState($w): string
    {
        if (Schema::hasColumn('workshops', 'status') && !empty($w->status)) {
            return strtolower($w->status); // upcoming/completed
        }
        return 'upcoming';
    }

    private function registrationsCount(int $workshopId): int
    {
        $q = WorkshopRegistration::where('workshop_id', $workshopId);

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $q->where('status', 'registered');
        }

        return $q->count();
    }

    public function index()
    {
        $companyId = Auth::id();

        $workshops = Workshop::query()
            ->where(function ($q) use ($companyId) {
                // ورش الشركة
                $q->where('company_user_id', $companyId);

                // ورش عامة (من الكلية/النظام) شرط تكون approved إذا العمود موجود
                $q->orWhere(function ($qq) {
                    $qq->whereNull('company_user_id');

                    if (Schema::hasColumn('workshops', 'proposal_status')) {
                        $qq->where('proposal_status', 'approved');
                    }
                });
            })
            ->orderByDesc('id')
            ->get()
            ->map(function ($w) {
                $state = $this->computeWorkshopState($w);

                $pill = match ($state) {
                    'completed' => ['completed', 'bg-secondary text-secondary-foreground'],
                    default => ['upcoming', 'bg-blue-500/15 text-blue-400'],
                };

                return [
                    'id' => $w->id,
                    'title' => $w->title ?? 'Workshop',
                    'date' => $w->date ?? ($w->workshop_date ?? ''),
                    'time' => $w->time ?? ($w->time_range ?? ''),
                    'location' => $w->location ?? ($w->venue ?? ''),
                    'state' => $pill[0],
                    'state_class' => $pill[1],
                    'registrations' => $this->registrationsCount($w->id),
                    'owned' => (int)($w->company_user_id ?? 0) === (int)Auth::id(),
                    'capacity' => Schema::hasColumn('workshops', 'capacity') ? ($w->capacity ?? null) : null,
                ];
            });

        return view('company.workshops.index', compact('workshops'));
    }

    public function create()
    {
        return view('company.workshops.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            'time' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $attrs = [
            'title' => $data['title'],
            'company_user_id' => Auth::id(),
        ];

        // proposal_status (إذا موجود)
        if (Schema::hasColumn('workshops', 'proposal_status')) {
            // حاليا approved مباشرة، لاحقاً ممكن نخليها pending وتحتاج موافقة كلية/أدمن
            $attrs['proposal_status'] = 'approved';
        }

        // الأعمدة الأساسية
        if (Schema::hasColumn('workshops', 'date')) $attrs['date'] = $data['date'];
        if (Schema::hasColumn('workshops', 'time')) $attrs['time'] = $data['time'];
        if (Schema::hasColumn('workshops', 'location')) $attrs['location'] = $data['location'];

        // capacity (إذا موجود) — null = unlimited
        if (Schema::hasColumn('workshops', 'capacity')) {
            $attrs['capacity'] = $data['capacity'] ?? null;
        }

        // status (إذا موجود)
        if (Schema::hasColumn('workshops', 'status')) {
            $attrs['status'] = 'upcoming';
        }

        Workshop::create($attrs);

        return redirect()->route('company.workshops')->with('toast_success', 'Workshop proposed successfully.');
    }

    public function manage(Workshop $workshop)
    {
        // السماح: لو الورشة عامة أو تابعة للشركة
        if (!is_null($workshop->company_user_id) && (int)$workshop->company_user_id !== (int)Auth::id()) {
            abort(403);
        }

        $regs = WorkshopRegistration::with('alumni')
            ->where('workshop_id', $workshop->id)
            ->orderByDesc('id');

        // ✅ اعرض المسجلين فقط (بدون cancelled)
        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $regs->where('status', 'registered');
        }

        $registrations = $regs->get();

        return view('company.workshops.manage', compact('workshop', 'registrations'));
    }
}
