<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        // Ringkasan status total
        $total   = Document::count();
        $pending = Document::where('status', 'PENDING')->count();
        $done    = Document::where('status', 'DONE')->count();
        $failed  = Document::where('status', 'FAILED')->count();

        // Range waktu
        $today   = Carbon::today();
        $week    = Carbon::now()->startOfWeek();
        $month   = Carbon::now()->startOfMonth();

        // KPI waktu singkat
        $createdToday = Document::whereDate('date', $today)->count();
        $createdWeek  = Document::whereDate('date', '>=', $week)->count();
        $createdMonth = Document::whereDate('date', '>=', $month)->count();

        // Line chart: 12 bulan terakhir (jumlah dokumen per bulan)
        $months = collect(range(11, 0))->map(fn($i) => Carbon::now()->startOfMonth()->subMonths($i));
        $lineLabels = $months->map(fn($m) => $m->format('Y-m'));
        $lineData = $months->map(function ($m) {
            return Document::whereBetween('date', [$m->copy(), $m->copy()->endOfMonth()])->count();
        });

        $overdueDays = 7;
        $pendingOverdue = \App\Models\Document::where('status', 'PENDING')
            ->where('date', '<', now()->subDays($overdueDays))
            ->count();

        $donut = [
            'labels' => ['PENDING', 'DONE', 'FAILED'],
            'data'   => [$pending, $done, $failed],
        ];


        // Bar chart: distribusi status bulan ini
        $barLabels = ['PENDING', 'DONE', 'FAILED'];
        $barData = [
            Document::where('status', 'PENDING')->whereDate('date', '>=', $month)->count(),
            Document::where('status', 'DONE')->whereDate('date', '>=', $month)->count(),
            Document::where('status', 'FAILED')->whereDate('date', '>=', $month)->count(),
        ];

        // Tabel terbaru + rows/page (10/15/25/50)
        $per = (int) $request->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50]) ? $per : 15;

        $latest = Document::orderByDesc('date')->orderByDesc('id')
            ->paginate($per)->withQueryString();

        return view('dashboard', [
            'overdueDays'    => $overdueDays,
            'pendingOverdue' => $pendingOverdue,
            'donut'          => $donut,
            'title'         => 'Dashboard',
            'total'         => $total,
            'pending'       => $pending,
            'done'          => $done,
            'failed'        => $failed,
            'createdToday'  => $createdToday,
            'createdWeek'   => $createdWeek,
            'createdMonth'  => $createdMonth,
            'lineLabels'    => $lineLabels,
            'lineData'      => $lineData,
            'barLabels'     => $barLabels,
            'barData'       => $barData,
            'latest'        => $latest,
            'per_page'      => $per,

        ]);
    }
}
