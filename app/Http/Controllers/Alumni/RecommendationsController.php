<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $received = Recommendation::where('to_user_id', $userId)
            ->orderByDesc('id')
            ->get();

        $given = Recommendation::where('from_user_id', $userId)
            ->orderByDesc('id')
            ->get();

        // Dropdown alumni list (excluding current)
        $alumniList = User::where('role', 'alumni')
            ->where('id', '!=', $userId)
            ->orderBy('name')
            ->get(['id', 'name', 'academic_id', 'email']);

        return view('alumni.recommendations', compact('received', 'given', 'alumniList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'role_title' => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string', 'max:2000'],
        ]);

        $toUser = User::where('role', 'alumni')->findOrFail($data['to_user_id']);

        Recommendation::create([
            'from_user_id' => $user->id,
            'to_user_id'   => $toUser->id,
            'from_name'    => $user->name,
            'to_name'      => $toUser->name,
            'role_title'   => $data['role_title'],
            'content'      => $data['content'],
            'date'         => now()->format('M d, Y'),
        ]);

        return back()->with('toast_success', 'Recommendation sent successfully!');
    }

    public function destroy(Recommendation $recommendation)
    {
        // Only delete your own "given" recommendation
        if ($recommendation->from_user_id !== Auth::id()) {
            abort(403);
        }

        $recommendation->delete();

        return back()->with('toast_success', 'Recommendation deleted.');
    }
}
