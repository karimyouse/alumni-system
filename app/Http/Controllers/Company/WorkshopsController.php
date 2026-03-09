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
            return strtolower($w->status);
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

                $q->where('company_user_id', $companyId);


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
            'title'    => ['required','string','max:255'],
            'date'     => ['required','string','max:255'],
            'time'     => ['required','string','max:255'],
            'location' => ['required','string','max:255'],
            'capacity' => ['nullable','integer','min:1'], // optional
        ]);

        $cap = $data['capacity'] ?? null; // null => unlimited

        $attrs = [
            'title'          => $data['title'],
            'company_user_id'=> Auth::id(),
        ];


        if (Schema::hasColumn('workshops', 'organizer_user_id')) $attrs['organizer_user_id'] = Auth::id();
        if (Schema::hasColumn('workshops', 'organizer_role'))    $attrs['organizer_role'] = 'company';


        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $attrs['proposal_status'] = 'approved';
        }


        if (Schema::hasColumn('workshops', 'date'))     $attrs['date'] = $data['date'];
        if (Schema::hasColumn('workshops', 'time'))     $attrs['time'] = $data['time'];
        if (Schema::hasColumn('workshops', 'location')) $attrs['location'] = $data['location'];

        if (Schema::hasColumn('workshops', 'status')) $attrs['status'] = 'upcoming';


        if (Schema::hasColumn('workshops', 'capacity')) {
            $attrs['capacity'] = $cap;
        }
        if (Schema::hasColumn('workshops', 'max_spots')) {
            $attrs['max_spots'] = $cap ? (int)$cap : 0;
        }
        if (Schema::hasColumn('workshops', 'spots')) {
            $attrs['spots'] = $cap ? (int)$cap : 0; 
        }

        Workshop::create($attrs);

        return redirect()->route('company.workshops')
            ->with('toast_success', 'Workshop proposed successfully.');
    }

    public function manage(Workshop $workshop)
    {
        if (!is_null($workshop->company_user_id) && (int)$workshop->company_user_id !== (int)Auth::id()) {
            abort(403);
        }

        $regs = WorkshopRegistration::with('alumni')
            ->where('workshop_id', $workshop->id)
            ->orderByDesc('id');

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $regs->where('status', 'registered');
        }

        $registrations = $regs->get();

        return view('company.workshops.manage', compact('workshop', 'registrations'));
    }
}
