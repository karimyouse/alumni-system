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

class SuccessStoriesController extends Controller
{
    public function index()
    {
        $stories = SuccessStory::query()
            ->orderByDesc('id')
            ->paginate(10);

        $stories->getCollection()->transform(function ($story) {
            $story->display_status_label = $story->is_published ? 'published' : 'draft';
            $story->display_status_class = $story->is_published
                ? 'bg-primary text-primary-foreground'
                : 'bg-secondary text-secondary-foreground';

            $story->display_created = $story->created_at?->format('M d, Y') ?? '—';
            $story->display_views = isset($story->views_count) ? (int) $story->views_count : 0;

            $name = trim((string) ($story->name ?? 'Alumni'));
            $story->display_initials = collect(explode(' ', $name))
                ->filter()
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->join('') ?: 'A';

            return $story;
        });

        return view('college.success-stories', array_merge(
            compact('stories'),
            $this->buildNavCounts()
        ));
    }

    public function create()
    {
        return view('college.success-stories-create', array_merge([
            'story' => null,
            'isEdit' => false,
        ], $this->buildNavCounts()));
    }

    public function store(Request $request)
    {
        $data = $this->validateStory($request);
        $publishNow = $request->boolean('is_published');

        SuccessStory::create([
            'name' => trim($data['name']),
            'graduation_year' => trim($data['graduation_year']),
            'current_position' => !empty($data['current_position']) ? trim($data['current_position']) : null,
            'title' => trim($data['title']),
            'body' => trim($data['body']),
            'is_published' => $publishNow,
            'published_at' => $publishNow ? now() : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('college.successStories')
            ->with('toast_success', $publishNow ? 'Story published.' : 'Story saved as draft.');
    }

    public function edit(SuccessStory $story)
    {
        return view('college.success-stories-create', array_merge([
            'story' => $story,
            'isEdit' => true,
        ], $this->buildNavCounts()));
    }

    public function update(Request $request, SuccessStory $story)
    {
        $data = $this->validateStory($request);
        $publishNow = $request->boolean('is_published');

        $story->update([
            'name' => trim($data['name']),
            'graduation_year' => trim($data['graduation_year']),
            'current_position' => !empty($data['current_position']) ? trim($data['current_position']) : null,
            'title' => trim($data['title']),
            'body' => trim($data['body']),
            'is_published' => $publishNow,
            'published_at' => $publishNow ? ($story->published_at ?: now()) : null,
        ]);

        return redirect()
            ->route('college.successStories')
            ->with('toast_success', $publishNow ? 'Story updated.' : 'Draft updated.');
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

    public function destroy(SuccessStory $story)
    {
        $story->delete();

        return back()->with('toast_success', 'Story deleted.');
    }

    private function validateStory(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'graduation_year' => ['required', 'string', 'max:50'],
            'current_position' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'is_published' => ['nullable'],
        ]);
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
