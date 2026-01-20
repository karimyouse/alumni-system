<?php

namespace App\Http\Middleware;

use App\Models\CompanyProfile;
use Closure;
use Illuminate\Http\Request;

class EnsureCompanyApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'company') {
            return $next($request);
        }

        $profile = CompanyProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            // لا نحذف أي شيء — فقط نعرض Pending
            return response()->view('company.pending-approval', [
                'companyName' => $user->name ?? 'Company',
            ], 403);
        }

        if ($profile->status === 'approved') {
            return $next($request);
        }

        if ($profile->status === 'rejected') {
            return response()->view('company.rejected', [
                'companyName' => $profile->company_name,
                'adminNote'   => $profile->admin_note,
            ], 403);
        }

        // pending
        return response()->view('company.pending-approval', [
            'companyName' => $profile->company_name,
        ], 403);
    }
}
