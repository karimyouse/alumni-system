<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkshopsController extends Controller
{
    public function index()
    {
        $workshops = Workshop::query()
            ->withCount([
                'registrations as registered_count' => function ($q) {
                    if (Schema::hasColumn('workshop_registrations', 'status')) {
                        $q->where('status', 'registered');
                    }
                }
            ])
            ->orderByDesc('id')
            ->paginate(10)
            ->through(function ($workshop) {
                $workshop->display_status = $this->resolveWorkshopStatus($workshop);
                $workshop->display_capacity = $this->resolveWorkshopCapacity($workshop);
                $workshop->display_registered = (int) ($workshop->registered_count ?? 0);
                $workshop->display_spots_label = $this->buildSpotsLabel($workshop);
                return $workshop;
            });

        return view('college.workshops', compact('workshops'));
    }

    public function create()
    {
        return view('college.workshops.create', [
            'workshop' => null,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateWorkshop($request);

        $attrs = $this->buildWorkshopAttributes($data, null);

        Workshop::create($attrs);

        return redirect()
            ->route('college.workshops')
            ->with('toast_success', 'Workshop created successfully.');
    }

    public function edit(Workshop $workshop)
    {
        return view('college.workshops.create', [
            'workshop' => $workshop,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Workshop $workshop)
    {
        $data = $this->validateWorkshop($request);

        $attrs = $this->buildWorkshopAttributes($data, $workshop);

        $workshop->update($attrs);

        return redirect()
            ->route('college.workshops')
            ->with('toast_success', 'Workshop updated successfully.');
    }

    public function manage(Workshop $workshop)
    {
        $q = WorkshopRegistration::with('alumni')
            ->where('workshop_id', $workshop->id)
            ->orderByDesc('id');

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $q->where('status', 'registered');
        }

        $registrations = $q->get();

        $workshop->registered_count = $registrations->count();
        $workshop->display_capacity = $this->resolveWorkshopCapacity($workshop);
        $workshop->display_spots_label = $this->buildSpotsLabel($workshop);
        $workshop->display_status = $this->resolveWorkshopStatus($workshop);

        return view('college.workshops-manage', compact('workshop', 'registrations'));
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();

        return back()->with('toast_success', 'Workshop deleted successfully.');
    }

    private function validateWorkshop(Request $request): array
    {
        return $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'date'     => ['required', 'string', 'max:255'],
            'time'     => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function buildWorkshopAttributes(array $data, ?Workshop $existing): array
    {
        $cap = $data['capacity'] ?? null;

        $attrs = [
            'title' => $data['title'],
        ];

        if (Schema::hasColumn('workshops', 'date')) {
            $attrs['date'] = $data['date'];
        }

        if (Schema::hasColumn('workshops', 'time')) {
            $attrs['time'] = $data['time'];
        }

        if (Schema::hasColumn('workshops', 'location')) {
            $attrs['location'] = $data['location'];
        }

        if (Schema::hasColumn('workshops', 'organizer_user_id') && !$existing) {
            $attrs['organizer_user_id'] = Auth::id();
        }

        if (Schema::hasColumn('workshops', 'organizer_role') && !$existing) {
            $attrs['organizer_role'] = 'college';
        }

        if (Schema::hasColumn('workshops', 'company_user_id') && !$existing) {
            $attrs['company_user_id'] = null;
        }

        if (Schema::hasColumn('workshops', 'status')) {
            $attrs['status'] = 'upcoming';
        }

        if (Schema::hasColumn('workshops', 'proposal_status')) {
            $attrs['proposal_status'] = 'approved';
        }

        if (Schema::hasColumn('workshops', 'capacity')) {
            $attrs['capacity'] = $cap;
        }

        if (Schema::hasColumn('workshops', 'max_spots')) {
            $attrs['max_spots'] = $cap ? (int) $cap : 0;
        }

        if (Schema::hasColumn('workshops', 'spots')) {
            $registered = 0;

            if ($existing) {
                $registered = WorkshopRegistration::query()
                    ->where('workshop_id', $existing->id)
                    ->when(
                        Schema::hasColumn('workshop_registrations', 'status'),
                        fn ($q) => $q->where('status', 'registered')
                    )
                    ->count();
            }

            $attrs['spots'] = $cap ? max(((int) $cap - $registered), 0) : 0;
        }

        return $attrs;
    }

    private function resolveWorkshopCapacity(Workshop $workshop): ?int
    {
        if (isset($workshop->capacity) && !is_null($workshop->capacity)) {
            return (int) $workshop->capacity;
        }

        if (isset($workshop->max_spots) && (int) $workshop->max_spots > 0) {
            return (int) $workshop->max_spots;
        }

        return null;
    }

    private function buildSpotsLabel(Workshop $workshop): string
    {
        $registered = (int) ($workshop->registered_count ?? 0);
        $capacity = $this->resolveWorkshopCapacity($workshop);

        if (!$capacity) {
            return $registered . ' registered';
        }

        return $registered . '/' . $capacity . ' registered';
    }

    private function resolveWorkshopStatus(Workshop $workshop): string
    {
        if (!empty($workshop->status)) {
            return strtolower((string) $workshop->status);
        }

        if (!empty($workshop->date)) {
            try {
                $date = Carbon::parse($workshop->date);
                return $date->isPast() ? 'completed' : 'upcoming';
            } catch (\Throwable $e) {
                return 'upcoming';
            }
        }

        return 'upcoming';
    }
}
