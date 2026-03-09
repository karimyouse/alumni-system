<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkshopsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();


        $workshops = Workshop::query()
            ->withCount([
                'registrations as registered_count' => function ($q) {

                if (Schema::hasColumn('workshop_registrations', 'status')) {
                        $q->where('status', 'registered');
                    }
                }
            ])
            ->orderByDesc('id')
            ->get();


            $regQuery = WorkshopRegistration::where('alumni_user_id', $userId);

        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $regQuery->where('status', 'registered');
        }

        $registeredIds = $regQuery->pluck('workshop_id')->toArray();

        return view('alumni.workshops', compact('workshops', 'registeredIds'));
    }

    public function register(Workshop $workshop)
    {
        $userId = Auth::id();


        if (Schema::hasColumn('workshops', 'proposal_status')) {
            if (($workshop->proposal_status ?? 'approved') !== 'approved') {
                return back()->with('toast_success', 'This workshop is not available yet.');
            }
        }


        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $already = WorkshopRegistration::where('workshop_id', $workshop->id)
                ->where('alumni_user_id', $userId)
                ->where('status', 'registered')
                ->exists();

            if ($already) {
                return back()->with('toast_success', 'You are already registered.');
            }
        }


        if (Schema::hasColumn('workshops', 'capacity')) {
            $cap = $workshop->capacity;

            if (!is_null($cap)) {
                $countQuery = WorkshopRegistration::where('workshop_id', $workshop->id);

                if (Schema::hasColumn('workshop_registrations', 'status')) {
                    $countQuery->where('status', 'registered');
                }

                $count = $countQuery->count();

                if ($count >= (int)$cap) {
                    return back()->with('toast_success', 'Workshop is full.');
                }
            }
        }


        $attrs = [
            'workshop_id' => $workshop->id,
            'alumni_user_id' => $userId,
        ];

        $values = [];


        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $values['status'] = 'registered';
        }

        WorkshopRegistration::updateOrCreate($attrs, $values);

        return back()->with('toast_success', 'Successfully registered!');
    }

    public function cancel(Workshop $workshop)
    {
        $userId = Auth::id();

        $reg = WorkshopRegistration::where('workshop_id', $workshop->id)
            ->where('alumni_user_id', $userId)
            ->first();

        if (!$reg) {
            return back()->with('toast_success', 'No registration found.');
        }


        if (Schema::hasColumn('workshop_registrations', 'status')) {
            $reg->update(['status' => 'cancelled']);
        } else {

        $reg->delete();
        }

        return back()->with('toast_success', 'Registration cancelled.');
    }
}
