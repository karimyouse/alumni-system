<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\Scholarship;
use App\Models\SuccessStory;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementsController extends Controller
{
    public function index()
    {
        $announcements = Announcement::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('college.announcements', array_merge(
            compact('announcements'),
            $this->buildNavCounts()
        ));
    }

    public function create()
    {
        return view('college.announcements-create', array_merge([
            'announcement' => null,
            'isEdit' => false,
        ], $this->buildNavCounts()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'audience' => ['required', 'in:all,alumni,company,college'],
            'is_published' => ['nullable'],
        ]);

        $publishNow = $request->boolean('is_published');

        Announcement::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'audience' => $data['audience'],
            'is_published' => $publishNow,
            'published_at' => $publishNow ? now() : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('college.announcements')
            ->with('toast_success', $publishNow ? 'Announcement published.' : 'Announcement saved as draft.');
    }

    public function edit(Announcement $announcement)
    {
        return view('college.announcements-create', array_merge([
            'announcement' => $announcement,
            'isEdit' => true,
        ], $this->buildNavCounts()));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'audience' => ['required', 'in:all,alumni,company,college'],
            'is_published' => ['nullable'],
        ]);

        $publishNow = $request->boolean('is_published');

        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'audience' => $data['audience'],
            'is_published' => $publishNow,
            'published_at' => $publishNow ? ($announcement->published_at ?: now()) : null,
        ]);

        return redirect()
            ->route('college.announcements')
            ->with('toast_success', $publishNow ? 'Announcement updated.' : 'Draft updated.');
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

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('toast_success', 'Announcement deleted.');
    }

    private function buildNavCounts(): array
    {
        return [
            'alumniBadgeCount' => User::where('role', 'alumni')->count(),
            'workshopBadgeCount' => Workshop::count(),
            'jobBadgeCount' => Job::count(),
            'announcementBadgeCount' => Announcement::count(),
            'scholarshipBadgeCount' => Scholarship::count(),
            'successStoryBadgeCount' => SuccessStory::count(),
        ];
    }
}
