<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $query = User::query()
            ->where('role', 'alumni')
            ->with('alumniProfile');

        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('academic_id', 'like', "%{$q}%");
            });
        }

        $alumni = $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('college.alumni-management', compact('alumni', 'q'));
    }

    public function show(User $alumnus)
    {
        if ($alumnus->role !== 'alumni') abort(404);

        $alumnus->load('alumniProfile');

        return view('college.alumni-show', compact('alumnus'));
    }
}
