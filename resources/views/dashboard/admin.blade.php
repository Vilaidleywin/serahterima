@extends('layouts.app')

@push('styles')
    <style>
        /* ===== Enterprise Theme ===== */
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --border: #e6e9f2;
            --shadow: 0 10px 30px rgba(2, 6, 23, .06);

            --ink: #0b1220;
            /* heading */
            --muted: #64748b;
            /* secondary text */
            --soft: #94a3b8;
            /* tertiary */

            --primary: #1e3a8a;
            /* navy */
            --primary-600: #2746a6;
            --accent: #0ea5a4;
            /* teal */
            --success: #10b981;
            /* green */
            --warning: #f59e0b;
            /* amber */
            --neutral: #475569;
            /* slate-600 */
        }

        body {
            background: var(--bg)
        }

        .wrap {
            max-width: 1280px;
            margin: 0 auto
        }

        .head {
            color: var(--ink);
            font-weight: 800;
            letter-spacing: .2px
        }

        .subhead {
            color: var(--muted)
        }

        /* ===== Cards / Tiles ===== */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow)
        }

        .tile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 16px;
            color: #fff;
            box-shadow: 0 8px 20px rgba(2, 6, 23, .08);
            position: relative;
            overflow: hidden;
        }

        .tile:after {
            content: "";
            position: absolute;
            inset: auto -30% -60% auto;
            width: 140%;
            height: 140%;
            background: linear-gradient(135deg, rgba(255, 255, 255, .18), rgba(255, 255, 255, 0));
            transform: rotate(8deg);
        }

        .tile .v {
            font-size: 2rem;
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: .2px
        }

        .tile .l {
            font-weight: 700;
            opacity: .95
        }

        .tile.navy {
            background: var(--primary)
        }

        .tile.green {
            background: var(--success)
        }

        .tile.slate {
            background: var(--neutral)
        }

        .tile.amber {
            background: #fde68a;
            color: #1f2937
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .28rem .6rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .8rem;
            background: #e2e8f0;
            color: #0f172a;
            border: 1px solid #d5dbe6;
        }

        .chip.brand {
            background: rgba(14, 165, 164, .12);
            color: #0b766c;
            border-color: rgba(14, 165, 164, .28)
        }

        .btn-link {
            font-weight: 700;
            text-decoration: none;
            color: var(--primary)
        }

        .btn-link:hover {
            text-decoration: underline;
            color: var(--primary-600)
        }

        /* ===== Chart (compact) ===== */
        .chart-compact {
            height: 200px
        }

        /* ===== “Pengguna Terbaru” – Enterprise Mini List ===== */
        .mini-list {
            --row-br: var(--border)
        }

        /* header baris mini-list: sama grid-nya dengan baris data */
        .mini-list .headrow {
            display: grid;
            grid-template-columns: 46px 1.4fr 1fr 120px 94px;
            /* Nama lebih lebar */
            gap: .6rem;
            align-items: center;
            background: #f8fafc;
            border: 1px solid var(--row-br);
            border-radius: 12px;
            padding: .55rem .75rem;
            color: #0f172a;
            font-weight: 700;
        }

        .mini-list .row {
            display: grid;
            grid-template-columns: 46px 1.4fr 1fr 120px 94px;
            /* Sama biar sejajar */
            gap: .6rem;
            align-items: center;
            background: var(--card);
            border: 1px solid var(--row-br);
            border-radius: 12px;
            padding: .5rem .75rem;
            margin-top: .55rem;
            transition: .16s ease box-shadow, .16s ease transform;
        }


        .mini-list .row:hover {
            box-shadow: 0 6px 18px rgba(2, 6, 23, .06);
            transform: translateY(-1px)
        }

        .mini-list .cell {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .mini-list .name {
            font-weight: 600;
            color: var(--ink)
        }

        .mini-list .muted {
            color: var(--muted)
        }

        .mini-list .role {
            display: inline-block;
            padding: .18rem .55rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .76rem;
            border: 1px solid rgba(2, 6, 23, .08);
            background: linear-gradient(180deg, rgba(30, 58, 138, .08), rgba(30, 58, 138, .04));
            color: #102a6b;
        }

        .mini-list .role.admin {
            background: linear-gradient(180deg, rgba(14, 165, 164, .18), rgba(14, 165, 164, .06));
            color: #0b766c;
            border-color: rgba(14, 165, 164, .25);
        }

        .mini-list .status {
            padding: .16rem .48rem;
            border-radius: 10px;
            font-weight: 800;
            font-size: .78rem;
        }

        .mini-list .ok {
            background: rgba(16, 185, 129, .15);
            color: #0a6b4e;
            border: 1px solid rgba(16, 185, 129, .35)
        }

        .mini-list .off {
            background: #eef2f7;
            color: #475569;
            border: 1px solid #e2e8f0
        }

        .empty {
            color: #94a3b8;
            text-align: center;
            padding: .75rem 0
        }

        /* === Tabel Pengguna Terbaru (gaya default, rapi & compact) === */
        .table-clean thead th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            border-bottom: 1px solid var(--border);
            padding: .55rem .75rem;
        }

        .table-clean tbody td {
            padding: .55rem .75rem;
            vertical-align: middle;
        }

        .badge-ok {
            background: #DCFCE7;
            color: #065F46;
            border-radius: 10px;
            padding: .15rem .5rem;
            font-weight: 700
        }

        .badge-off {
            background: #E5E7EB;
            color: #374151;
            border-radius: 10px;
            padding: .15rem .5rem;
            font-weight: 700
        }

        .chip-role {
            background: #E2E8F0;
            color: #0F172A;
            border: 1px solid #D5DBE6;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            padding: .15rem .55rem
        }
    </style>
@endpush

@section('content')
    <div class="wrap">
        <div class="mb-2">
            <h2 class="head mb-0">Manajemen Pengguna</h2>
            <div class="subhead">Ringkasan dan kontrol akun pengguna sistem</div>
        </div>

        {{-- ===== KPIs ===== --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="tile navy h-100">
                    <div class="l">Total Pengguna</div>
                    <div class="v">{{ $totalUsers }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="tile green h-100">
                    <div class="l">User Aktif</div>
                    <div class="v">{{ $activeUsers }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="tile slate h-100">
                    <div class="l">User Nonaktif</div>
                    <div class="v">{{ $inactiveUsers }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="tile amber h-100 d-flex justify-content-between">
                    <div>
                        <div class="l" style="color:#111827">Petugas</div>
                        <div class="subhead" style="font-weight:600">Role Terbanyak</div>
                    </div>
                    <span class="chip brand">{{ $topRole ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- ===== Chart + Latest Users ===== --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-7">
                <div class="card p-3 h-100">
                    <div class="fw-semibold mb-2">Aktivitas Pengguna per Bulan</div>
                    <div class="chart-compact"><canvas id="userLine"></canvas></div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold">Pengguna Terbaru</div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users.create') }}" class="btn-link small">Tambah</a>
                            <a href="{{ route('admin.users.index') }}" class="btn-link small">Lihat Semua</a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-clean table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th style="width:140px">Role</th>
                                    <th style="width:110px">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestUsers as $i => $u)
                                    @php
                                        $role = $u->role ?? 'user';
                                        $on = property_exists($u, 'is_active_view') ? (bool) $u->is_active_view : true;
                                      @endphp
                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $u->name }}</td>
                                        <td class="text-muted">{{ $u->username ?? '—' }}</td>
                                        <td><span class="chip-role">{{ $role }}</span></td>
                                        <td>
                                            <span
                                                class="{{ $on ? 'badge-ok' : 'badge-off' }}">{{ $on ? 'Aktif' : 'Nonaktif' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===== Info Cards ===== --}}
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card p-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="chip">i</span>
                        <div><strong>{{ $newThisWeek }}</strong> pengguna baru ditambahkan minggu ini.</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="chip">i</span>
                        <div><strong>{{ $deactivatedThisWeek }}</strong> akun dinonaktifkan minggu ini.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const el = document.getElementById('userLine'); if (!el) return;
            const labels = @json($lineLabels);
            const data = @json($lineData);

            const ctx = el.getContext('2d');

            // gradient brand navy -> transparent
            const g = ctx.createLinearGradient(0, 0, 0, 200);
            g.addColorStop(0, 'rgba(30,58,138,.22)');  // primary 20%
            g.addColorStop(1, 'rgba(30,58,138,0)');

            new Chart(el, {
                type: 'line',
                data: {
                    labels, datasets: [{
                        data,
                        borderColor: 'rgba(30,58,138,1)',   // primary
                        backgroundColor: g,
                        fill: true, borderWidth: 2, tension: .35, pointRadius: 3, pointHoverRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index', intersect: false,
                            backgroundColor: '#0b1220', titleColor: '#fff', bodyColor: '#e5e7eb',
                            padding: 10, borderWidth: 0,
                            callbacks: { label: (ctx) => ` ${ctx.parsed.y} pengguna` }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#475569', maxRotation: 0, autoSkip: true }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, stepSize: 1, color: '#475569' },
                            grid: { color: 'rgba(2,6,23,.06)' }
                        }
                    }
                }
            });
        })();
    </script>
@endpush