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
        $totalUsers = User::count();

        // Deteksi kolom status yang tersedia
        $hasIsActive   = Schema::hasColumn('users', 'is_active');
        $hasActive     = Schema::hasColumn('users', 'active');
        $hasStatus     = Schema::hasColumn('users', 'status');       // contoh nilai: 'Aktif' / 'Nonaktif'
        $hasDisabledAt = Schema::hasColumn('users', 'disabled_at');  // nullable datetime

        // Hitung aktif/nonaktif secara robust
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

        // Role terbanyak
        $roleColumn = Schema::hasColumn('users', 'role') ? 'role' : null;
        $topRole = $roleColumn
            ? User::select($roleColumn, DB::raw('COUNT(*) jml'))
                ->groupBy($roleColumn)
                ->orderByDesc('jml')
                ->value($roleColumn)
            : '-';

        // ==== Divisi terbanyak (ini yang dipakai kartu kuning) ====
        $divisionColumn = Schema::hasColumn('users', 'division') ? 'division' : null;

        $topDivision = $divisionColumn
            ? User::select($divisionColumn, DB::raw('COUNT(*) jml'))
                ->whereNotNull($divisionColumn)
                ->groupBy($divisionColumn)
                ->orderByDesc('jml')
                ->value($divisionColumn)
            : null;
        // ==========================================================

        // Pilih sumber aktivitas: tabel user_logins (jika ada) atau fallback ke created_at user
        $useLoginTable = Schema::hasTable('user_logins');

        // --------- BULANAN (12 bulan terakhir) ----------
        $startMonth = now()->startOfMonth()->subMonths(11);
        $months = collect(range(0, 11))->map(function ($i) use ($startMonth) {
            return $startMonth->copy()->addMonths($i);
        });

        if ($useLoginTable) {
            $rawMonth = DB::table('user_logins')
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
                ->where('created_at', '>=', $startMonth)
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total', 'ym');
        } else {
            $rawMonth = User::where('created_at', '>=', $startMonth)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total', 'ym');
        }

        $lineLabelsMonthly = $months->map(fn($m) => $m->translatedFormat('M Y'))->values();
        $lineDataMonthly = $months->map(fn($m) => $rawMonth[$m->format('Y-m')] ?? 0)->values();

        // --------- MINGGUAN (12 minggu terakhir) ----------
        $startWeek = now()->startOfWeek()->subWeeks(11);
        $weeks = collect(range(0, 11))->map(function ($i) use ($startWeek) {
            $wkStart = $startWeek->copy()->addWeeks($i);
            $wkEnd = $wkStart->copy()->endOfWeek();
            return ['start' => $wkStart, 'end' => $wkEnd];
        });

        $lineLabelsWeekly = $weeks->map(fn($w) => $w['start']->format('d M') . ' - ' . $w['end']->format('d M'))->values();

        $lineDataWeekly = $weeks->map(function ($w) use ($useLoginTable) {
            $s = $w['start']->startOfDay();
            $e = $w['end']->endOfDay();
            if ($useLoginTable) {
                return DB::table('user_logins')
                    ->whereBetween('created_at', [$s, $e])
                    ->count();
            } else {
                return User::whereBetween('created_at', [$s, $e])->count();
            }
        })->values();

        // --------- HARIAN (30 hari terakhir) ----------
        $startDay = now()->startOfDay()->subDays(29); // total 30 days
        $days = collect(range(0, 29))->map(fn($i) => $startDay->copy()->addDays($i));

        $lineLabelsDaily = $days->map(fn($d) => $d->translatedFormat('d M'))->values();
        $lineDataDaily = $days->map(function ($d) use ($useLoginTable) {
            $s = $d->copy()->startOfDay();
            $e = $d->copy()->endOfDay();
            if ($useLoginTable) {
                return DB::table('user_logins')
                    ->whereBetween('created_at', [$s, $e])
                    ->count();
            } else {
                return User::whereBetween('created_at', [$s, $e])->count();
            }
        })->values();

        // Pengguna terbaru
        $selectCols = collect(['id', 'name', 'username', 'role', 'status', 'is_active', 'active', 'disabled_at'])
            ->filter(fn($c) => Schema::hasColumn('users', $c))
            ->values()
            ->all();

        if (!in_array('id', $selectCols)) {
            $selectCols[] = 'id';
        }
        if (!in_array('name', $selectCols)) {
            $selectCols[] = 'name';
        }

        $latestUsers = User::latest()
            ->take(5)
            ->get($selectCols)
            ->map(function ($u) {
                $isActive = null;

                if (isset($u->is_active)) {
                    $isActive = (bool) $u->is_active;
                } elseif (isset($u->active)) {
                    $isActive = (bool) $u->active;
                } elseif (isset($u->status)) {
                    $isActive = in_array($u->status, ['Aktif', 'active', 'ACTIVE', 1, '1', true], true);
                } elseif (isset($u->disabled_at)) {
                    $isActive = $u->disabled_at === null;
                }

                $u->is_active_view = $isActive ?? true;

                return $u;
            });

        // Ringkasan minggu ini
        $weekStart = Carbon::now()->startOfWeek();
        $newThisWeek = User::where('created_at', '>=', $weekStart)->count();
        $deactivatedThisWeek = $hasDisabledAt
            ? User::where('disabled_at', '>=', $weekStart)->count()
            : ($hasIsActive
                ? User::where('updated_at', '>=', $weekStart)->where('is_active', 0)->count()
                : ($hasActive
                    ? User::where('updated_at', '>=', $weekStart)->where('active', 0)->count()
                    : ($hasStatus
                        ? User::where('updated_at', '>=', $weekStart)->whereIn('status', ['Nonaktif', 'inactive', 'INACTIVE', 0, '0', false])->count()
                        : 0)));

        // === Ringkasan HARI INI: total login & user unik yg login ===
        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        if ($useLoginTable) {
            $totalLoginsToday = DB::table('user_logins')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();

            $uniqueLoginsToday = DB::table('user_logins')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->distinct('user_id')
                ->count('user_id');
        } else {
            $uniqueLoginsToday = User::whereBetween('created_at', [$todayStart, $todayEnd])->count();
            $totalLoginsToday  = $uniqueLoginsToday;
        }

        // === Ringkasan bulan ini: total login & user unik yg login ===
        $monthStart = now()->startOfMonth();

        if ($useLoginTable) {
            $totalLoginsThisMonth = DB::table('user_logins')
                ->where('created_at', '>=', $monthStart)
                ->count();

            $uniqueLoginsThisMonth = DB::table('user_logins')
                ->where('created_at', '>=', $monthStart)
                ->distinct('user_id')
                ->count('user_id');
        } else {
            $uniqueLoginsThisMonth = User::where('created_at', '>=', $monthStart)->count();
            $totalLoginsThisMonth  = $uniqueLoginsThisMonth;
        }

        // === USER ONLINE: last_seen dalam N menit terakhir ===
        $onlineWindowMinutes = 5;

        $onlineUsers = User::whereNotNull('last_seen')
            ->where('last_seen', '>=', now()->subMinutes($onlineWindowMinutes))
            ->count();

        return view('dashboard.admin', [
            'title'                 => 'Manajemen Pengguna',
            'totalUsers'            => $totalUsers,
            'activeUsers'           => $activeUsers,
            'inactiveUsers'         => $inactiveUsers,
            'topRole'               => $topRole ?? '-',
            'topDivision'           => $topDivision,
            // kirim semua dataset
            'lineLabelsMonthly'     => $lineLabelsMonthly,
            'lineDataMonthly'       => $lineDataMonthly,
            'lineLabelsWeekly'      => $lineLabelsWeekly,
            'lineDataWeekly'        => $lineDataWeekly,
            'lineLabelsDaily'       => $lineLabelsDaily,
            'lineDataDaily'         => $lineDataDaily,
            // fallback lama
            'lineLabels'            => $lineLabelsMonthly,
            'lineData'              => $lineDataMonthly,
            'latestUsers'           => $latestUsers,
            'newThisWeek'           => $newThisWeek,
            'deactivatedThisWeek'   => $deactivatedThisWeek,
            // HARI INI
            'totalLoginsToday'      => $totalLoginsToday,
            'uniqueLoginsToday'     => $uniqueLoginsToday,
            // BULAN INI
            'totalLoginsThisMonth'  => $totalLoginsThisMonth,
            'uniqueLoginsThisMonth' => $uniqueLoginsThisMonth,
            // initial view default: daily
            'initialView'           => 'daily',
            // USER ONLINE
            'onlineUsers'           => $onlineUsers,
        ]);
    }

    /**
     * Endpoint kecil buat AJAX realtime "User Online"
     */
    public function onlineUsersCount()
    {
        $onlineWindowMinutes = 5;

        $onlineUsers = User::whereNotNull('last_seen')
            ->where('last_seen', '>=', now()->subMinutes($onlineWindowMinutes))
            ->count();

        return response()->json([
            'online' => $onlineUsers,
        ]);
    }
}
