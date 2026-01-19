<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Support\Facades\Auth;

class ScholarshipsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $scholarships = Scholarship::orderByDesc('id')->paginate(10);

        $appliedIds = ScholarshipApplication::where('alumni_user_id', $userId)
            ->pluck('scholarship_id')
            ->toArray();

        return view('alumni.scholarships', compact('scholarships', 'appliedIds'));
    }

    public function show(Scholarship $scholarship)
    {
        $userId = Auth::id();

        $alreadyApplied = ScholarshipApplication::where('alumni_user_id', $userId)
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        return view('alumni.scholarship-show', compact('scholarship', 'alreadyApplied'));
    }

    public function apply(Scholarship $scholarship)
    {
        $userId = Auth::id();

        $exists = ScholarshipApplication::where('alumni_user_id', $userId)
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        if ($exists) {
            return back()->with('toast_success', 'You already applied for this scholarship.');
        }

        ScholarshipApplication::create([
            'scholarship_id' => $scholarship->id,
            'alumni_user_id' => $userId,
            'status' => 'pending',
            'applied_date' => now()->format('M d, Y'),
        ]);

        return redirect()->route('alumni.scholarships.show', $scholarship)
            ->with('toast_success', 'Scholarship application submitted!');
    }
}
