<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q',''));

        $query = Scholarship::query()->orderByDesc('id');

        if ($q !== '') {
            $query->where('title', 'like', "%{$q}%");
        }

        $scholarships = $query->paginate(10)->withQueryString();

        return view('college.scholarships', compact('scholarships', 'q'));
    }

    public function create()
    {
        return view('college.scholarships-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'deadline' => ['nullable','string','max:255'],
            'amount' => ['nullable','string','max:50'],
            'description' => ['nullable','string','max:5000'],
        ]);

        Scholarship::create($data);

        return redirect()->route('college.scholarships')->with('toast_success', 'Scholarship created successfully.');
    }

    public function show(Scholarship $scholarship)
    {
        return view('college.scholarships-show', compact('scholarship'));
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return back()->with('toast_success', 'Scholarship deleted.');
    }
}
