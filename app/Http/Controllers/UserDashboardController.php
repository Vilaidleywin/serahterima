<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userDivision = $user->division ?? null;

        // selalu return fresh Query Builder
        $docFactory = function () use ($userDivision) {
            $q = Document::query();
            if ($userDivision) {
                $q->where('division', $userDivision);
            }
            return $q;
        };

        // Ringkasan status
        $draft     = $docFactory()->where('status', 'DRAFT')->count();
        $submitted = $docFactory()->where('status', 'SUBMITTED')->count();
        $rejected  = $docFactory()->where('status', 'REJECTED')->count();
        $total     = $draft + $submitted + $rejected;

        // Range waktu
        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();
        $month = Carbon::now()->startOfMonth();

        $createdToday = $docFactory()->whereDate('date', $today)->count();
        $createdWeek  = $docFactory()->whereDate('date', '>=', $week)->count();
        $createdMonth = $docFactory()->whereDate('date', '>=', $month)->count();

        // Line chart 12 bulan
        $months = collect(range(11, 0))->map(
            fn($i) => Carbon::now()->startOfMonth()->subMonths($i)
        );

        $lineLabels = $months
            ->map(fn($m) => $m->format('Y-m'))
            ->values()
            ->toArray();

        $lineData = $months
            ->map(function ($m) use ($docFactory) {
                return $docFactory()
                    ->whereBetween('date', [
                        $m->copy()->startOfMonth(),
                        $m->copy()->endOfMonth(),
                    ])
                    ->count();
            })
            ->values()
            ->toArray();

        $lineRanges = $months
            ->map(function ($m) {
                return [
                    'start' => $m->copy()->startOfMonth()->toDateString(),
                    'end'   => $m->copy()->endOfMonth()->toDateString(),
                ];
            })
            ->values()
            ->toArray();

        // Overdue SUBMITTED
        $overdueDays = 7;
        $submittedOverdue = $docFactory()
            ->where('status', 'SUBMITTED')
            ->where('date', '<', now()->subDays($overdueDays))->count();

        // Bar chart bulan ini
        $barLabels = ['DRAFT', 'SUBMITTED', 'REJECTED'];
        $barData = [
            $docFactory()->where('status', 'DRAFT')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            $docFactory()->where('status', 'SUBMITTED')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            $docFactory()->where('status', 'REJECTED')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
        ];

        // Donut
        $donut = [
            'labels' => ['SUBMITTED', 'REJECTED', 'DRAFT'],
            'data'   => [$submitted, $rejected, $draft],
        ];

        // Tabel terbaru
        $per = (int) $request->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50]) ? $per : 15;

        $latest = $docFactory()
            ->orderByDesc('date')->orderByDesc('id')
            ->paginate($per)->withQueryString();

        $destinations = $docFactory()->select('destination')->distinct()->pluck('destination')->filter()->values();

        return view('dashboard', [
            'title'            => 'Dashboard',
            'total'            => $total,
            'draft'            => $draft,
            'submitted'        => $submitted,
            'rejected'         => $rejected,
            'createdToday'     => $createdToday,
            'createdWeek'      => $createdWeek,
            'createdMonth'     => $createdMonth,
            'overdueDays'      => $overdueDays,
            'submittedOverdue' => $submittedOverdue,
            'lineLabels'       => $lineLabels,
            'lineData'         => $lineData,
            'lineRanges'       => $lineRanges,
            'barLabels'        => $barLabels,
            'barData'          => $barData,
            'donut'            => $donut,
            'latest'           => $latest,
            'per_page'         => $per,
            'divisions'        => $destinations,
        ]);
    }
}
