<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScholarshipsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Scholarship::query()
            ->withCount('applications')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where('title', 'like', "%{$q}%");
        }

        $scholarships = $query->paginate(10)->withQueryString();

        $scholarships->getCollection()->transform(function ($scholarship) {
            $scholarship->display_badge = $this->resolveScholarshipBadge($scholarship->deadline);
            $scholarship->display_amount = $this->formatAmount($scholarship->amount);
            $scholarship->display_deadline = $scholarship->deadline ?: '—';
            $scholarship->display_applicants = (int) ($scholarship->applications_count ?? 0);

            return $scholarship;
        });

        return view('college.scholarships', compact('scholarships', 'q'));
    }

    public function create()
    {
        return view('college.scholarships-create', [
            'scholarship' => null,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateScholarship($request);

        Scholarship::create([
            'title' => $data['title'],
            'deadline' => $data['deadline'] ?: null,
            'amount' => $data['amount'] ?: null,
            'description' => $data['description'] ?: null,
            'created_by_user_id' => auth()->id(),
            'status' => 'active',
        ]);

        return redirect()
            ->route('college.scholarships')
            ->with('toast_success', 'Scholarship created successfully.');
    }

    public function edit(Scholarship $scholarship)
    {
        return view('college.scholarships-create', [
            'scholarship' => $scholarship,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $data = $this->validateScholarship($request);

        $scholarship->update([
            'title' => $data['title'],
            'deadline' => $data['deadline'] ?: null,
            'amount' => $data['amount'] ?: null,
            'description' => $data['description'] ?: null,
        ]);

        return redirect()
            ->route('college.scholarships')
            ->with('toast_success', 'Scholarship updated successfully.');
    }

    public function applicants(Scholarship $scholarship)
    {
        $scholarship->load([
            'applications.alumni',
        ]);

        $scholarship->applications_count = $scholarship->applications->count();
        $scholarship->display_badge = $this->resolveScholarshipBadge($scholarship->deadline);
        $scholarship->display_amount = $this->formatAmount($scholarship->amount);
        $scholarship->display_deadline = $scholarship->deadline ?: '—';

        return view('college.scholarships-applicants', compact('scholarship'));
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();

        return back()->with('toast_success', 'Scholarship deleted.');
    }

    private function validateScholarship(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function formatAmount(?string $amount): string
    {
        $amount = trim((string) $amount);

        if ($amount === '') {
            return '—';
        }

        if (str_starts_with($amount, '$')) {
            return $amount;
        }

        return '$ ' . $amount;
    }

    private function resolveScholarshipBadge(?string $deadline): ?array
    {
        if (!$deadline) {
            return null;
        }

        try {
            $date = Carbon::parse($deadline)->startOfDay();
            $today = now()->startOfDay();

            if ($date->lt($today)) {
                return [
                    'label' => 'Closed',
                    'class' => 'bg-secondary text-secondary-foreground',
                ];
            }

            if ($today->diffInDays($date, false) <= 14) {
                return [
                    'label' => 'Closing Soon',
                    'class' => 'bg-red-500/10 text-red-400',
                ];
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}
