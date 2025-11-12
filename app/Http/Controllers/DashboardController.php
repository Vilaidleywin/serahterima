<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->isAdmin()
            ? $this->adminView()
            : $this->userView($request);
    }

    /** =========================== ADMIN DASHBOARD =========================== */
    protected function adminView()
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
            // fallback: semua dianggap aktif (biar dashboard tetap jalan)
            $activeUsers   = $totalUsers;
            $inactiveUsers = 0;
        }

        // Role terbanyak (ubah jika pakai relasi roles milik spatie)
        $roleColumn = Schema::hasColumn('users', 'role') ? 'role' : null;
        $topRole = $roleColumn
            ? User::select($roleColumn, DB::raw('COUNT(*) jml'))
            ->groupBy($roleColumn)->orderByDesc('jml')->value($roleColumn)
            : '-';

        // Aktivitas per bulan (user baru)
        $start = now()->startOfMonth()->subMonths(11);
        $agg   = User::where('created_at', '>=', $start)
            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') ym"), DB::raw('COUNT(*) jml'))
            ->groupBy('ym')->orderBy('ym')->pluck('jml', 'ym');

        $months     = collect(range(0, 11))->map(fn($i) => $start->copy()->addMonths($i));
        $lineLabels = $months->map(fn($d) => $d->format('Y-m'))->values();
        $lineData   = $lineLabels->map(fn($ym) => $agg[$ym] ?? 0)->values();

        // Pengguna terbaru (pilih kolom yang ada)
        $selectCols = collect(['id', 'name', 'username', 'role', 'status', 'is_active', 'active', 'disabled_at'])
            ->filter(fn($c) => Schema::hasColumn('users', $c))->values()->all();
        if (!in_array('id', $selectCols))   $selectCols[] = 'id';
        if (!in_array('name', $selectCols)) $selectCols[] = 'name';

        $latestUsers = User::latest()->take(5)->get($selectCols)->map(function ($u) {
            $isActive = null;
            if (isset($u->is_active))         $isActive = (bool) $u->is_active;
            elseif (isset($u->active))        $isActive = (bool) $u->active;
            elseif (isset($u->status))        $isActive = in_array($u->status, ['Aktif', 'active', 'ACTIVE', 1, '1', true], true);
            elseif (isset($u->disabled_at))   $isActive = $u->disabled_at === null;
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

        return view('dashboard.admin', [
            'title'               => 'Manajemen Pengguna',
            'totalUsers'          => $totalUsers,
            'activeUsers'         => $activeUsers,
            'inactiveUsers'       => $inactiveUsers,
            'topRole'             => $topRole ?? '-',
            'lineLabels'          => $lineLabels,
            'lineData'            => $lineData,
            'latestUsers'         => $latestUsers,
            'newThisWeek'         => $newThisWeek,
            'deactivatedThisWeek' => $deactivatedThisWeek,
        ]);
    }

    /** =========================== USER DASHBOARD =========================== */
    protected function userView(Request $request)
    {
        // === Ringkasan status total ===
        $draft     = Document::where('status', 'DRAFT')->count();
        $submitted = Document::where('status', 'SUBMITTED')->count();
        $rejected  = Document::where('status', 'REJECTED')->count();
        $total     = $draft + $submitted + $rejected;

        // === Range waktu ===
        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();
        $month = Carbon::now()->startOfMonth();

        // === KPI ===
        $createdToday = Document::whereDate('date', $today)->count();
        $createdWeek  = Document::whereDate('date', '>=', $week)->count();
        $createdMonth = Document::whereDate('date', '>=', $month)->count();

        // === Line chart: 12 bulan ===
        $months     = collect(range(11, 0))->map(fn($i) => Carbon::now()->startOfMonth()->subMonths($i));
        $lineLabels = $months->map(fn($m) => $m->format('Y-m'))->values()->toArray();
        $lineData   = $months->map(fn($m) => Document::whereBetween('date', [$m->copy(), $m->copy()->endOfMonth()])->count())
            ->values()->toArray();

        // === Overdue SUBMITTED ===
        $overdueDays = 7;
        $submittedOverdue = Document::where('status', 'SUBMITTED')
            ->where('date', '<', now()->subDays($overdueDays))->count();

        // === Bar (bulan ini) ===
        $barLabels = ['DRAFT', 'SUBMITTED', 'REJECTED'];
        $barData = [
            Document::where('status', 'DRAFT')->whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
            Document::where('status', 'SUBMITTED')->whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
            Document::where('status', 'REJECTED')->whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
        ];

        // === Donut (urut SUBMITTED, REJECTED, DRAFT agar cocok dengan legend warna di view) ===
        $donut = [
            'labels' => ['SUBMITTED', 'REJECTED', 'DRAFT'],
            'data'   => [$submitted, $rejected, $draft],
        ];

        // === Tabel terbaru + per-page ===
        $per = (int) $request->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50]) ? $per : 15;

        $latest = Document::orderByDesc('date')->orderByDesc('id')
            ->paginate($per)->withQueryString();

        // ====== RETURN (pakai satu array saja) ======
        $data = [
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
            'barLabels'        => $barLabels,
            'barData'          => $barData,
            'donut'            => $donut,
            'latest'           => $latest,
            'per_page'         => $per,
        ];

        return view('dashboard', $data);
    }

    /** =========================== Helper =========================== */
    protected function isAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Jika pakai spatie/laravel-permission
        if (method_exists($user, 'hasRole')) {
            try {
                return $user->hasRole(['admin_internal', 'admin_komersial']);
            } catch (\Throwable $e) {
                // fallback ke kolom role
            }
        }

        // Fallback: kolom 'role' di tabel users
        $role = (string) ($user->role ?? '');
        return in_array($role, ['admin_internal', 'admin_komersial'], true);
    }
}
