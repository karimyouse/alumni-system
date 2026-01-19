<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkshopsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $workshops = Workshop::orderByDesc('id')->get();

        $registeredIds = WorkshopRegistration::where('alumni_user_id', $userId)
            ->pluck('workshop_id')
            ->toArray();

        return view('alumni.workshops', compact('workshops', 'registeredIds'));
    }

    public function register(Workshop $workshop)
    {
        $userId = Auth::id();

        $exists = WorkshopRegistration::where('workshop_id', $workshop->id)
            ->where('alumni_user_id', $userId)
            ->exists();

        if ($exists) {
            return back()->with('toast_success', 'You are already registered.');
        }

        DB::transaction(function () use ($workshop, $userId) {
            $w = Workshop::lockForUpdate()->find($workshop->id);

            if ($w->spots <= 0) {
                throw new \RuntimeException('No spots left');
            }

            WorkshopRegistration::create([
                'workshop_id' => $w->id,
                'alumni_user_id' => $userId,
                'status' => 'registered',
            ]);

            $w->decrement('spots');
        });

        return back()->with('toast_success', 'Registered successfully!');
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

        DB::transaction(function () use ($workshop, $reg) {
            $w = Workshop::lockForUpdate()->find($workshop->id);
            $reg->delete();
            $w->increment('spots');
        });

        return back()->with('toast_success', 'Registration cancelled.');
    }
}
