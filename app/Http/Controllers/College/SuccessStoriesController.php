<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SuccessStoriesController extends Controller
{
    public function index()
    {
        $stories = SuccessStory::orderByDesc('id')->paginate(10);
        return view('college.success-stories', compact('stories'));
    }

    public function create()
    {
        $alumni = User::where('role', 'alumni')->orderBy('name')->get(['id','name']);
        return view('college.success-stories-create', compact('alumni'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['required','string','max:5000'],
            'alumni_user_id' => ['nullable','integer'],
        ]);

        SuccessStory::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'alumni_user_id' => $data['alumni_user_id'] ?? null,
            'is_published' => true,
            'published_at' => now(),
            'created_by' => auth::id(),
        ]);

        return redirect()->route('college.successStories')->with('toast_success', 'Success story created.');
    }

    public function toggle(SuccessStory $story)
    {
        $new = !$story->is_published;

        $story->update([
            'is_published' => $new,
            'published_at' => $new ? now() : null,
        ]);

        return back()->with('toast_success', $new ? 'Story published.' : 'Story unpublished.');
    }
}
