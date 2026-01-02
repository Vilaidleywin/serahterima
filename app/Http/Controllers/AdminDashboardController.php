<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ================= BASIC COUNT =================
        $totalUsers = User::count();

        $hasIsActive   = Schema::hasColumn('users', 'is_active');
        $hasActive     = Schema::hasColumn('users', 'active');
        $hasStatus     = Schema::hasColumn('users', 'status');
        $hasDisabledAt = Schema::hasColumn('users', 'disabled_at');

        if ($hasIsActive) {
            $activeUsers   = User::where('is_active', 1)->count();
            $inactiveUsers = User::where('is_active', 0)->count();
        } elseif ($hasActive) {
            $activeUsers   = User::where('active', 1)->count();
            $inactiveUsers = User::where('active', 0)->count();
        } elseif ($hasStatus) {
            $activeUsers   = User::whereIn('status', ['Aktif', 'active', 'ACTIVE', 1, '1', true])->count();
            $inactiveUsers = User::whereIn('status', ['Nonaktif', 'inactive', 'INACTIVE', 0, '0', false])->count();
        } elseif ($hasDisabledAt) {
            $activeUsers   = User::whereNull('disabled_at')->count();
            $inactiveUsers = User::whereNotNull('disabled_at')->count();
        } else {
            $activeUsers   = $totalUsers;
            $inactiveUsers = 0;
        }

        // ================= TOP ROLE & DIVISION =================
        $topRole = Schema::hasColumn('users', 'role')
            ? User::select('role', DB::raw('COUNT(*) jml'))
            ->groupBy('role')
            ->orderByDesc('jml')
            ->value('role')
            : '-';

        $topDivision = Schema::hasColumn('users', 'division')
            ? User::select('division', DB::raw('COUNT(*) jml'))
            ->whereNotNull('division')
            ->groupBy('division')
            ->orderByDesc('jml')
            ->value('division')
            : null;

        // ================= ACTIVITY SOURCE =================
        $useLoginTable = Schema::hasTable('user_logins');

        // ================= MONTHLY =================
        $startMonth = now()->startOfMonth()->subMonths(11);
        $months = collect(range(0, 11))->map(fn($i) => $startMonth->copy()->addMonths($i));

        $rawMonth = $useLoginTable
            ? DB::table('user_logins')
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') ym, COUNT(*) total")
            ->where('created_at', '>=', $startMonth)
            ->groupBy('ym')
            ->pluck('total', 'ym')
            : User::where('created_at', '>=', $startMonth)
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') ym, COUNT(*) total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $lineLabelsMonthly = $months->map(fn($m) => $m->translatedFormat('M Y'));
        $lineDataMonthly   = $months->map(fn($m) => $rawMonth[$m->format('Y-m')] ?? 0);

        // ================= WEEKLY =================
        $startWeek = now()->startOfWeek()->subWeeks(11);
        $weeks = collect(range(0, 11))->map(fn($i) => [
            'start' => $startWeek->copy()->addWeeks($i),
            'end'   => $startWeek->copy()->addWeeks($i)->endOfWeek(),
        ]);

        $lineLabelsWeekly = $weeks->map(
            fn($w) =>
            $w['start']->format('d M') . ' - ' . $w['end']->format('d M')
        );

        $lineDataWeekly = $weeks->map(function ($w) use ($useLoginTable) {
            return $useLoginTable
                ? DB::table('user_logins')->whereBetween('created_at', [$w['start'], $w['end']])->count()
                : User::whereBetween('created_at', [$w['start'], $w['end']])->count();
        });

        // ================= DAILY =================
        $startDay = now()->startOfDay()->subDays(29);
        $days = collect(range(0, 29))->map(fn($i) => $startDay->copy()->addDays($i));

        $lineLabelsDaily = $days->map(fn($d) => $d->translatedFormat('d M'));
        $lineDataDaily   = $days->map(function ($d) use ($useLoginTable) {
            return $useLoginTable
                ? DB::table('user_logins')->whereDate('created_at', $d)->count()
                : User::whereDate('created_at', $d)->count();
        });

        $latestUsers = User::latest()
            ->take(5)
            ->get(['id', 'name', 'username', 'role', 'last_seen']);

        $weekStart = now()->startOfWeek();
        $newThisWeek = User::where('created_at', '>=', $weekStart)->count();

        $deactivatedThisWeek = $hasDisabledAt
            ? User::where('disabled_at', '>=', $weekStart)->count()
            : 0;

        $onlineUsers = User::where(
            'last_seen',
            '>=',
            now()->subMinutes(3)
        )->count();

        return view('dashboard.admin', [
            'totalUsers'          => $totalUsers,
            'activeUsers'         => $activeUsers,
            'inactiveUsers'       => $inactiveUsers,
            'topRole'             => $topRole,
            'topDivision'         => $topDivision,
            'lineLabelsMonthly'   => $lineLabelsMonthly,
            'lineDataMonthly'     => $lineDataMonthly,
            'lineLabelsWeekly'    => $lineLabelsWeekly,
            'lineDataWeekly'      => $lineDataWeekly,
            'lineLabelsDaily'     => $lineLabelsDaily,
            'lineDataDaily'       => $lineDataDaily,
            'latestUsers'         => $latestUsers,
            'newThisWeek'         => $newThisWeek,
            'deactivatedThisWeek' => $deactivatedThisWeek,
            'onlineUsers'         => $onlineUsers,
            'initialView'         => 'daily',
        ]);
    }

    
    public function onlineUsersCount()
    {
        return response()->json([
            'online' => User::where('last_seen', '>=', now()->subMinutes(3))->count(),
        ]);
    }
}
