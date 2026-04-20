<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\ContentReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContentController extends Controller
{

    private array $tabs = [
        'announcements' => [
            'table' => 'announcements',
            'title_cols' => ['title', 'subject', 'name'],
            'meta_cols'  => ['created_at'],
            'desc_cols'  => ['content', 'description', 'body'],
        ],
        'success_stories' => [
            'table' => 'success_stories',
            'title_cols' => ['title', 'name'],
            'meta_cols'  => ['created_at'],
            'desc_cols'  => ['story', 'content', 'description', 'body'],
        ],
        'workshops' => [
            'table' => 'workshops',
            'title_cols' => ['title'],
            'meta_cols'  => ['date', 'time', 'location'],
            'desc_cols'  => ['description'],
        ],
        'scholarships' => [
            'table' => 'scholarships',
            'title_cols' => ['title'],
            'meta_cols'  => ['deadline', 'amount'],
            'desc_cols'  => ['description', 'requirements'],
        ],
    ];

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'announcements');
        if (!array_key_exists($tab, $this->tabs)) $tab = 'announcements';

        $status = $request->query('status', 'all');

        $tabCounts = [];
        foreach ($this->tabs as $k => $cfg) {
            $tabCounts[$k] = DB::table($cfg['table'])->count();
        }

        $table = $this->tabs[$tab]['table'];
        $statusCol = $this->resolveStatusColumn($table);

        $query = DB::table($table)->orderByDesc('id');


        if ($status !== 'all' && $statusCol) {
            $query->where($statusCol, $status);
        }

        $items = $query->paginate(10)->withQueryString();

        return view('admin.content', compact('tab', 'status', 'items', 'tabCounts', 'statusCol'));
    }

    public function approve(string $type, int $id)
    {
        $this->ensureValidType($type);

        $table = $this->tabs[$type]['table'];
        $statusCol = $this->resolveStatusColumn($table);

        $row = DB::table($table)->where('id', $id)->first();
        abort_unless($row, 404);


        if (!$statusCol) {
            return back()->with('toast_success', 'Approved (no review status column found).');
        }


        $current = $row->{$statusCol} ?? null;
        if ($current === 'approved') {
            return back()->with('toast_success', 'This item is already approved.');
        }

        $update = [$statusCol => 'approved'];

        if (Schema::hasColumn($table, 'approved_at')) $update['approved_at'] = now();
        if (Schema::hasColumn($table, 'approved_by')) $update['approved_by'] = Auth::id();

        if (Schema::hasColumn($table, 'rejected_at')) $update['rejected_at'] = null;
        if (Schema::hasColumn($table, 'rejected_by')) $update['rejected_by'] = null;

        $noteCol = $this->resolveNoteColumn($table);
        if ($noteCol) $update[$noteCol] = null;

        DB::table($table)->where('id', $id)->update($update);


        $this->notifyOwner($type, $table, $row, 'approved', null);

        return back()->with('toast_success', 'Item approved successfully.');
    }

    public function reject(Request $request, string $type, int $id)
    {
        $this->ensureValidType($type);

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $table = $this->tabs[$type]['table'];
        $statusCol = $this->resolveStatusColumn($table);

        $row = DB::table($table)->where('id', $id)->first();
        abort_unless($row, 404);

        if (!$statusCol) {
            return back()->with('toast_success', 'Rejected (no review status column found).');
        }


        $current = $row->{$statusCol} ?? null;
        if ($current === 'rejected') {
            return back()->with('toast_success', 'This item is already rejected.');
        }

        $note = $data['admin_note'] ?? 'Rejected by admin.';

        $update = [$statusCol => 'rejected'];

        if (Schema::hasColumn($table, 'rejected_at')) $update['rejected_at'] = now();
        if (Schema::hasColumn($table, 'rejected_by')) $update['rejected_by'] = Auth::id();


        if (Schema::hasColumn($table, 'approved_at')) $update['approved_at'] = null;
        if (Schema::hasColumn($table, 'approved_by')) $update['approved_by'] = null;

        $noteCol = $this->resolveNoteColumn($table);
        if ($noteCol) $update[$noteCol] = $note;

        DB::table($table)->where('id', $id)->update($update);


        $this->notifyOwner($type, $table, $row, 'rejected', $note);

        return back()->with('toast_success', 'Item rejected (saved in database).');
    }

    private function ensureValidType(string $type): void
    {
        abort_unless(array_key_exists($type, $this->tabs), 404);
    }



    private function resolveStatusColumn(string $table): ?string
    {
        if (Schema::hasColumn($table, 'approval_status')) return 'approval_status';
        if (Schema::hasColumn($table, 'proposal_status')) return 'proposal_status';
        return null;
    }



    private function resolveNoteColumn(string $table): ?string
    {
        if (Schema::hasColumn($table, 'admin_note')) return 'admin_note';
        if (Schema::hasColumn($table, 'reject_reason')) return 'reject_reason';
        return null;
    }



    private function notifyOwner(string $type, string $table, object $row, string $status, ?string $adminNote): void
    {
        try {
            if (!SystemSetting::enabled('email_content_approval_alerts')) return;
            if (!Schema::hasTable('notifications')) return;

            $ownerId = $this->resolveOwnerId($type, $table, $row);
            if (!$ownerId) return;

            $owner = User::find($ownerId);
            if (!$owner) return;

            $titleValue = $this->resolveTitle($type, $row);
            $typeLabel = $this->typeLabel($type);

            $msg = $status === 'approved'
                ? "Your {$typeLabel} \"{$titleValue}\" has been approved."
                : "Your {$typeLabel} \"{$titleValue}\" has been rejected.";

            if ($status === 'rejected' && !empty($adminNote)) {
                $msg .= " Note: {$adminNote}";
            }

            $url = $this->resolveOwnerActionUrl($type, $row, $owner);

            $payload = [
                'kind' => 'content_review',
                'content_type' => $type,
                'content_id' => (int)($row->id ?? 0),
                'status' => $status,


                'title' => "{$typeLabel} {$status}",
                'body'  => $msg,
                'message' => $msg,
                'url'   => $url,

                'admin_note' => $adminNote,
                'icon' => $status === 'approved' ? 'check-circle-2' : 'x-circle',
            ];

            $owner->notify(new ContentReviewNotification($payload));
        } catch (\Throwable $e) {

            return;
        }
    }

    private function resolveOwnerId(string $type, string $table, object $row): ?int
    {

        if ($type === 'workshops') {
            if (Schema::hasColumn($table, 'organizer_user_id') && !empty($row->organizer_user_id)) {
                return (int)$row->organizer_user_id;
            }
            if (Schema::hasColumn($table, 'company_user_id') && !empty($row->company_user_id)) {
                return (int)$row->company_user_id;
            }
        }


        $candidates = ['created_by_user_id', 'user_id', 'author_user_id', 'owner_user_id'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col) && !empty($row->{$col})) {
                return (int)$row->{$col};
            }
        }

        return null;
    }

    private function resolveTitle(string $type, object $row): string
    {
        $cfg = $this->tabs[$type] ?? null;
        if (!$cfg) return 'Item';

        foreach (($cfg['title_cols'] ?? []) as $col) {
            if (!empty($row->{$col})) return (string)$row->{$col};
        }
        return 'Item';
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'announcements' => 'Announcement',
            'success_stories' => 'Success Story',
            'workshops' => 'Workshop',
            'scholarships' => 'Scholarship',
            default => 'Content',
        };
    }

    private function resolveOwnerActionUrl(string $type, object $row, User $owner): ?string
    {
        $id = (int)($row->id ?? 0);
        if ($id <= 0) return null;

        
        if ($type === 'workshops') {
            if ($owner->role === 'company') return "/company/workshops/{$id}";
            if ($owner->role === 'college') return "/college/workshops/{$id}/manage";
        }

        if ($type === 'scholarships') {
            if ($owner->role === 'college') return "/college/scholarships/{$id}";
        }

        if ($type === 'announcements') {
            if ($owner->role === 'college') return "/college/announcements";
        }

        if ($type === 'success_stories') {
            if ($owner->role === 'college') return "/college/success-stories";
        }

        return null;
    }
}
