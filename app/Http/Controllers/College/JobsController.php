<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all'); 
        $q = trim((string)$request->query('q',''));

        $query = Job::query()->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('title','like',"%{$q}%")
                  ->orWhere('company_name','like',"%{$q}%")
                  ->orWhere('location','like',"%{$q}%");
            });
        }

        if (Schema::hasColumn('jobs','approval_status') && $status !== 'all') {
            $query->where('approval_status', $status);
        }

        $jobs = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Job::count(),
            'approved' => Schema::hasColumn('jobs','approval_status') ? Job::where('approval_status','approved')->count() : 0,
            'pending'  => Schema::hasColumn('jobs','approval_status') ? Job::where('approval_status','pending')->count() : 0,
            'rejected' => Schema::hasColumn('jobs','approval_status') ? Job::where('approval_status','rejected')->count() : 0,
        ];

        return view('college.jobs', compact('jobs','status','q','counts'));
    }

    public function approve(\App\Models\Job $job)
{
    $job->forceFill([
        'approval_status' => 'approved',
        'approved_at' => now(),
        'approved_by' => auth::id(),
        'reject_reason' => null,
    ])->save();

    return back()->with('toast_success', 'Job approved.');
}

public function reject(\Illuminate\Http\Request $request, \App\Models\Job $job)
{
    $data = $request->validate([
        'reject_reason' => ['nullable','string','max:2000'],
    ]);

    $job->forceFill([
        'approval_status' => 'rejected',
        'approved_at' => null,
        'approved_by' => auth::id(),
        'reject_reason' => $data['reject_reason'] ?? 'Rejected by college.',
    ])->save();

    return back()->with('toast_success', 'Job rejected (saved in database).');
}

public function toggleFeatured(\App\Models\Job $job)
{
    $job->forceFill([
        'is_featured' => !(bool) $job->is_featured
    ])->save();

    return back()->with('toast_success', $job->is_featured ? 'Job featured.' : 'Job unfeatured.');
}


}
