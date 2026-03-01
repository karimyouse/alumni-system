<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
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
            ->paginate(10);

        // ✅ الصحيح: صفحة القائمة (مش create)
        return view('college.workshops', compact('workshops'));
    }

    public function create()
    {
        // ✅ الملف عندك: resources/views/college/workshops/create.blade.php
        return view('college.workshops.create');
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

        $cap = $data['capacity'] ?? null; // null => unlimited (we will store 0 where needed)

        $attrs = [
            'title' => $data['title'],
        ];

        if (Schema::hasColumn('workshops', 'date'))     $attrs['date'] = $data['date'];
        if (Schema::hasColumn('workshops', 'time'))     $attrs['time'] = $data['time'];
        if (Schema::hasColumn('workshops', 'location')) $attrs['location'] = $data['location'];

        // ✅ Organizer fields (THIS FIXES your NULL problem for new rows)
        if (Schema::hasColumn('workshops', 'organizer_user_id')) $attrs['organizer_user_id'] = Auth::id();
        if (Schema::hasColumn('workshops', 'organizer_role'))    $attrs['organizer_role'] = 'college';

        // public workshop => no company owner
        if (Schema::hasColumn('workshops', 'company_user_id')) $attrs['company_user_id'] = null;

        // status/proposal_status
        if (Schema::hasColumn('workshops', 'status'))          $attrs['status'] = 'upcoming';
        if (Schema::hasColumn('workshops', 'proposal_status')) $attrs['proposal_status'] = 'approved';

        /**
         * Capacity/Spots compatibility:
         * - some projects use capacity
         * - yours shows spots + max_spots in DB screenshot
         * We will store:
         *   max_spots = capacity (or 0 for unlimited)
         *   spots     = max_spots (initially remaining = total)
         */
        if (Schema::hasColumn('workshops', 'capacity')) {
            $attrs['capacity'] = $cap; // nullable allowed
        }
        if (Schema::hasColumn('workshops', 'max_spots')) {
            $attrs['max_spots'] = $cap ? (int)$cap : 0; // 0 => unlimited
        }
        if (Schema::hasColumn('workshops', 'spots')) {
            $attrs['spots'] = $cap ? (int)$cap : 0; // 0 => unlimited (UI will treat it as unlimited)
        }

        Workshop::create($attrs);

        return redirect()->route('college.workshops')
            ->with('toast_success', 'Workshop created successfully.');
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

        // ✅ عندك الملف: resources/views/college/workshops-manage.blade.php
        return view('college.workshops-manage', compact('workshop', 'registrations'));
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();
        return back()->with('toast_success', 'Workshop deleted.');
    }
}
