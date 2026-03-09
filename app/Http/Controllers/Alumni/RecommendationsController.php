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

        $received = Recommendation::query()
            ->where('to_user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                $name = $r->from_name ?: 'Alumni';
                $initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('');

                return (object) [
                    'id' => $r->id,
                    'name' => $name,
                    'initials' => $initials ?: 'A',
                    'role_title' => $r->role_title,
                    'content' => $r->content,
                    'date' => $r->date ?: optional($r->created_at)->format('M d, Y'),
                ];
            });

        $given = Recommendation::query()
            ->where('from_user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                $name = $r->to_name ?: 'Alumni';
                $initials = collect(explode(' ', $name))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->join('');

                return (object) [
                    'id' => $r->id,
                    'name' => $name,
                    'initials' => $initials ?: 'A',
                    'role_title' => $r->role_title,
                    'content' => $r->content,
                    'date' => $r->date ?: optional($r->created_at)->format('M d, Y'),
                ];
            });

        $alumniList = User::query()
            ->where('role', 'alumni')
            ->where('id', '!=', $userId)
            ->orderBy('name')
            ->get(['id', 'name', 'academic_id', 'email']);

        return view('alumni.recommendations', [
            'received' => $received,
            'given' => $given,
            'alumniList' => $alumniList,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'role_title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $toUser = User::query()
            ->where('role', 'alumni')
            ->where('id', '!=', $user->id)
            ->findOrFail($data['to_user_id']);

        Recommendation::create([
            'from_user_id' => $user->id,
            'to_user_id' => $toUser->id,
            'from_name' => $user->name,
            'to_name' => $toUser->name,
            'role_title' => $data['role_title'],
            'content' => $data['content'],
            'date' => now()->format('M d, Y'),
        ]);

        return back()->with('toast_success', 'Recommendation sent successfully!');
    }

    public function destroy(Recommendation $recommendation)
    {
        if ((int) $recommendation->from_user_id !== (int) Auth::id()) {
            abort(403);
        }

        $recommendation->delete();

        return back()->with('toast_success', 'Recommendation deleted.');
    }
}
