<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderByDesc('id')->paginate(10);
        return view('college.announcements', compact('announcements'));
    }

    public function create()
    {
        return view('college.announcements-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['required','string','max:5000'],
            'audience' => ['required','in:all,alumni,company,college'],
        ]);

        Announcement::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'audience' => $data['audience'],
            'is_published' => true,
            'published_at' => now(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('college.announcements')->with('toast_success','Announcement created.');
    }

    public function toggle(Announcement $announcement)
    {
        $new = !$announcement->is_published;

        $announcement->update([
            'is_published' => $new,
            'published_at' => $new ? now() : null,
        ]);

        return back()->with('toast_success', $new ? 'Announcement published.' : 'Announcement unpublished.');
    }
}
