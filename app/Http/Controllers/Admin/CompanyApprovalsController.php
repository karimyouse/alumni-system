<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyApprovalsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending|approved|rejected|all

        $query = CompanyProfile::with('user')->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $profiles = $query->paginate(10)->withQueryString();

        $counts = [
            'pending'  => CompanyProfile::where('status', 'pending')->count(),
            'approved' => CompanyProfile::where('status', 'approved')->count(),
            'rejected' => CompanyProfile::where('status', 'rejected')->count(),
            'all'      => CompanyProfile::count(),
        ];

        return view('admin.company-approvals', compact('profiles', 'counts', 'status'));
    }

    public function approve(CompanyProfile $profile)
    {
        // ✅ لا تعمل شيء إذا already approved
        if ($profile->status === 'approved') {
            return back()->with('toast_success', 'This company is already approved.');
        }

        $profile->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'rejected_at' => null,
            'approved_by' => Auth::id(),
            'admin_note'  => null,
        ]);

        return back()->with('toast_success', 'Company approved successfully.');
    }

    public function reject(Request $request, CompanyProfile $profile)
    {
        // ✅ لا تعمل شيء إذا already rejected
        if ($profile->status === 'rejected') {
            return back()->with('toast_success', 'This company is already rejected.');
        }

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $profile->update([
            'status'      => 'rejected',
            'rejected_at' => now(),
            'approved_at' => null,
            'approved_by' => Auth::id(),
            'admin_note'  => $data['admin_note'] ?? 'Rejected by admin.',
        ]);

        return back()->with('toast_success', 'Company rejected (saved in database).');
    }
}
