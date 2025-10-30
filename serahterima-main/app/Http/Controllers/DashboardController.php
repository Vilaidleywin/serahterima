<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === Ringkasan status total ===
        $total     = Document::count();

        // Kalau status di DB kadang "SUBMITED", aktifkan baris whereIn dan matikan where tunggal
        $submitted = Document::where('status', 'SUBMITTED')->count();
        // $submitted = Document::whereIn('status', ['SUBMITTED','SUBMITED'])->count();

        $rejected  = Document::where('status', 'REJECTED')->count();

        // === Range waktu ===
        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();
        $month = Carbon::now()->startOfMonth();

        // === KPI waktu singkat ===
        $createdToday = Document::whereDate('date', $today)->count();
        $createdWeek  = Document::whereDate('date', '>=', $week)->count();
        $createdMonth = Document::whereDate('date', '>=', $month)->count();

        // === Line chart: 12 bulan terakhir ===
        $months     = collect(range(11, 0))->map(fn($i) => Carbon::now()->startOfMonth()->subMonths($i));
        $lineLabels = $months->map(fn($m) => $m->format('Y-m'))->values()->toArray();
        $lineData   = $months->map(function ($m) {
            return Document::whereBetween('date', [$m->copy(), $m->copy()->endOfMonth()])->count();
        })->values()->toArray();

        // === Overdue SUBMITTED ===
        $overdueDays = 7;
        $submittedOverdue = Document::where('status', 'SUBMITTED')
            ->where('date', '<', now()->subDays($overdueDays))
            ->count();

        // === Donut & Bar chart data ===
        $donut = [
            'labels' => ['SUBMITTED', 'REJECTED'],
            'data'   => [$submitted, $rejected],
        ];

        $barLabels = ['SUBMITTED', 'REJECTED'];
        $barData = [
            Document::where('status', 'SUBMITTED')->whereDate('date', '>=', $month)->count(),
            Document::where('status', 'REJECTED')->whereDate('date', '>=', $month)->count(),
        ];

        // === Tabel terbaru + rows/page (10/15/25/50) ===
        $per = (int) $request->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50]) ? $per : 15;

        $latest = Document::orderByDesc('date')->orderByDesc('id')
            ->paginate($per)->withQueryString();

        return view('dashboard', [
            'title'            => 'Dashboard',
            'total'            => $total,
            'submitted'        => $submitted,   // <= pakai lowercase
            'rejected'         => $rejected,    // <= pakai lowercase
            'createdToday'     => $createdToday,
            'createdWeek'      => $createdWeek,
            'createdMonth'     => $createdMonth,
            'overdueDays'      => $overdueDays,
            'submittedOverdue' => $submittedOverdue,
            'lineLabels'       => $lineLabels,
            'lineData'         => $lineData,
            'barLabels'        => $barLabels,
            'barData'          => $barData,
            'donut'            => $donut,
            'latest'           => $latest,
            'per_page'         => $per,
        ]);
    }
}
