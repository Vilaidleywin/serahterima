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

        $docFactory = function () use ($userDivision) {
            $q = Document::query();
            if ($userDivision) {
                $q->where('division', $userDivision);
            }
            return $q;
        };

        $draft     = $docFactory()->where('status', 'DRAFT')->count();
        $submitted = $docFactory()->where('status', 'SUBMITTED')->count();
        $rejected  = $docFactory()->where('status', 'REJECTED')->count();
        $total     = $draft + $submitted + $rejected;

        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();
        $month = Carbon::now()->startOfMonth();

        $createdToday = $docFactory()->whereDate('date', $today)->count();
        $createdWeek  = $docFactory()->whereDate('date', '>=', $week)->count();
        $createdMonth = $docFactory()->whereDate('date', '>=', $month)->count();

        /* ======================= MONTHLY ======================= */
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

        $lineLabelsMonthly = $lineLabels;
        $lineDataMonthly   = $lineData;
        $lineRangesMonthly = $lineRanges;  // <-- FIX WAJIB ADA


        /* ======================= DAILY ======================= */
        $days = collect(range(29, 0))->map(
            fn($i) => Carbon::today()->subDays($i)
        );

        $lineLabelsDaily = $days
            ->map(fn($d) => $d->format('d M'))
            ->values()
            ->toArray();

        $lineDataDaily = $days
            ->map(fn($d) =>
                $docFactory()->whereDate('date', $d)->count()
            )
            ->values()
            ->toArray();

        $lineRangesDaily = $days
            ->map(fn($d) => [
                'start' => $d->toDateString(),
                'end'   => $d->toDateString(),
            ])
            ->values()
            ->toArray();

        /* ======================= WEEKLY ======================= */
        $weeks = collect(range(11, 0))->map(
            fn($i) => Carbon::now()->startOfWeek()->subWeeks($i)
        );

        $lineLabelsWeekly = $weeks
            ->map(fn($w) => 'Minggu ' . $w->format('d M'))
            ->values()
            ->toArray();

        $lineDataWeekly = $weeks
            ->map(function ($w) use ($docFactory) {
                return $docFactory()
                    ->whereBetween('date', [
                        $w->copy()->startOfWeek(),
                        $w->copy()->endOfWeek(),
                    ])
                    ->count();
            })
            ->values()
            ->toArray();

        $lineRangesWeekly = $weeks
            ->map(fn($w) => [
                'start' => $w->copy()->startOfWeek()->toDateString(),
                'end'   => $w->copy()->endOfWeek()->toDateString(),
            ])
            ->values()
            ->toArray();


        /* ======================= OTHER STATS ======================= */

        $overdueDays = 7;

        $submittedOverdue = $docFactory()
            ->where('status', 'SUBMITTED')
            ->where('date', '<', now()->subDays($overdueDays))
            ->count();

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

        $donut = [
            'labels' => ['SUBMITTED', 'REJECTED', 'DRAFT'],
            'data'   => [$submitted, $rejected, $draft],
        ];

        $per = (int) $request->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50]) ? $per : 15;

        $latest = $docFactory()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($per)
            ->withQueryString();

        $destinations = $docFactory()
            ->select('destination')
            ->distinct()
            ->pluck('destination')
            ->filter()
            ->values();

        $initialView = 'monthly';

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

            /* DAILY */
            'lineLabelsDaily'   => $lineLabelsDaily,
            'lineDataDaily'     => $lineDataDaily,
            'lineRangesDaily'   => $lineRangesDaily,

            /* WEEKLY */
            'lineLabelsWeekly'  => $lineLabelsWeekly,
            'lineDataWeekly'    => $lineDataWeekly,
            'lineRangesWeekly'  => $lineRangesWeekly,

            /* MONTHLY */
            'lineLabelsMonthly' => $lineLabelsMonthly,
            'lineDataMonthly'   => $lineDataMonthly,
            'lineRangesMonthly' => $lineRangesMonthly,

            /* OLD MONTHLY (masih dipakai beberapa chart) */
            'lineLabels' => $lineLabels,
            'lineData'   => $lineData,
            'lineRanges' => $lineRanges,

            'barLabels' => $barLabels,
            'barData'   => $barData,
            'donut'     => $donut,
            'latest'    => $latest,
            'per_page'  => $per,
            'divisions' => $destinations,
            'initialView' => $initialView,
        ]);
    }
}
