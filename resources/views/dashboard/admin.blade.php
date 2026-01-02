@extends('layouts.app')

@push('styles')
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --border: #e6e9f2;
            --shadow: 0 10px 30px rgba(2, 6, 23, .06);
            --ink: #0b1220;
            --muted: #64748b;
            --soft: #94a3b8;
            --primary: #1e3a8a;
            --primary-600: #2746a6;
            --accent: #0ea5a4;
            --success: #10b981;
            --warning: #f59e0b;
            --neutral: #475569;
        }

        body {
            background: var(--bg);
        }

        .wrap {
            max-width: 1280px;
            margin: 0 auto;
        }

        .head {
            color: var(--ink);
            font-weight: 800;
        }

        .subhead {
            color: var(--muted);
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .btn-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            padding: 2px 4px;
            border-radius: 6px;
            transition: .2s ease;
        }

        .btn-link:hover {
            color: var(--primary-600);
            background: rgba(30, 58, 138, .08);
            text-decoration: underline;
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
            font-weight: 800;
        }

        .tile .l {
            font-weight: 700;
        }

        .tile.navy {
            background: var(--primary);
        }

        .tile.green {
            background: var(--success);
        }

        .tile.slate {
            background: var(--neutral);
        }

        .tile.amber {
            background: #fde68a;
            color: #1f2937;
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
            border-color: rgba(14, 165, 164, .28);
        }

        .chart-compact {
            height: 200px;
        }

        .table-clean thead th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            border-bottom: 1px solid var(--border);
            padding: .55rem .75rem;
        }

        .table-clean tbody td {
            padding: .55rem .75rem;
        }

        .badge-ok {
            background: #DCFCE7;
            color: #065F46;
            border-radius: 10px;
            padding: .15rem .5rem;
            font-weight: 700;
        }

        .badge-soft-danger {
            background: #fee2e2 !important;
            border: 1px solid #fecaca !important;
            color: #991b1b !important;
        }

        .kpi-number {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--primary);
        }

        .kpi-label {
            font-size: .82rem;
            color: var(--muted);
            font-weight: 600;
        }

        .badge-soft {
            padding: .15rem .6rem;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 700;
            background: #e5edff;
            color: #1d2a6b;
            border: 1px solid #c7d2fe;
        }
    </style>
@endpush


@section('content')
    <div class="wrap">

        <div class="mb-2">
            <h2 class="head mb-0">Manajemen Pengguna</h2>
            <div class="subhead">Ringkasan dan kontrol akun pengguna sistem</div>
        </div>

        {{-- ================= KPIs ================== --}}
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

            {{-- 🔥 Tile yang lo minta ubah kemarin --}}
            <div class="col-lg-3 col-md-6">
                <div class="tile amber h-100">
                    <div>
                        <div class="l" style="color:#111827">Divisi Terbanyak</div>
                        <div class="subhead" style="font-weight:600">Pengguna</div>
                    </div>
                    <span class="chip brand">{{ $topDivision ?? '-' }}</span>
                </div>
            </div>

        </div>


        {{-- ================= CHART + LATEST ================== --}}
        <div class="row g-3 mb-3">

            <div class="col-lg-7">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="fw-semibold">Aktivitas Pengguna per Bulan</div>
                        <div class="text-muted small">Total login per bulan (12 bulan terakhir)</div>
                    </div>
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
                        <table class="table table-clean table-sm mb-0">
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
                                    {{-- @php
                                    $role = $u->role ?? 'user';
                                    $on = $u->is_online == 1;
                                    @endphp --}}

                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $u->name }}</td>
                                        <td class="text-muted">{{ $u->username ?? '—' }}</td>
                                        <td>{{ $u->role }}</td>
                                        <td>
                                            @if($u->is_online_now)
  <span class="badge bg-success">Online</span>
@else
  <span class="badge bg-secondary">Offline</span>
@endif

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


        {{-- ================= INFO CARDS ================== --}}
        <div class="row g-3">

            {{-- Pengguna baru --}}
            <div class="col-lg-4">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="kpi-label">Pengguna baru</span>
                        <span class="badge-soft">Minggu ini</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <div class="kpi-number">{{ $newThisWeek }}</div>
                            <div class="kpi-subtext">ditambahkan ke sistem</div>
                        </div>
                        <div class="kpi-subtext text-end">
                            Monitoring onboarding <br>pengguna.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Akun dinonaktifkan --}}
            <div class="col-lg-4">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="kpi-label">Akun dinonaktifkan</span>
                        <span class="badge-soft badge-soft-danger">MINGGU INI</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <div class="kpi-number" style="color:#b91c1c;">
                                {{ $deactivatedThisWeek }}
                            </div>
                            <div class="kpi-subtext">dalam minggu ini</div>
                        </div>
                        <div class="kpi-subtext text-end">
                            Periksa perubahan <br>role & izin.
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔥 USER ONLINE (REALTIME) --}}
            <div class="col-lg-4">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="kpi-label">User Online</span>
                        <span class="badge-soft"
                            style="background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.35);color:#047857;">
                            Realtime
                        </span>
                    </div>

                    <div class="mb-1">
                        <div class="kpi-number" id="onlineCount">{{ $onlineUsers }}</div>
                    </div>

                    <div class="kpi-subtext" style="border-top:1px dashed #e5e7eb;padding-top:.4rem;margin-top:.4rem;">
                        Auto-refresh setiap <strong>5 detik</strong>.
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
            const el = document.getElementById('userLine');
            if (!el) return;

            const fallbackLabels = @json($lineLabels ?? []);
            const fallbackData = @json($lineData ?? []);

            const datasets = {
                daily: { labels: @json($lineLabelsDaily), data: @json($lineDataDaily) },
                weekly: { labels: @json($lineLabelsWeekly), data: @json($lineDataWeekly) },
                monthly: { labels: @json($lineLabelsMonthly), data: @json($lineDataMonthly) }
            };

            Object.keys(datasets).forEach(k => {
                if (!datasets[k].labels || !datasets[k].data) {
                    datasets[k].labels = fallbackLabels;
                    datasets[k].data = fallbackData;
                }
            });

            const initialView = @json($initialView ?? 'monthly');

            const ctx = el.getContext('2d');
            const g = ctx.createLinearGradient(0, 0, 0, 200);
            g.addColorStop(0, 'rgba(30,58,138,.22)');
            g.addColorStop(1, 'rgba(30,58,138,0)');

            const chart = new Chart(el, {
                type: 'line',
                data: {
                    labels: datasets[initialView].labels,
                    datasets: [{
                        data: datasets[initialView].data,
                        borderColor: 'rgba(30,58,138,1)',
                        backgroundColor: g,
                        fill: true,
                        borderWidth: 2,
                        tension: .35,
                        pointRadius: 3,
                        pointHoverRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#475569' }, grid: { display: false } },
                        y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1, color: '#475569' } }
                    }
                }
            });

            // === DROPDOWN CHART SWITCHER (yang kemarin) ===
            (function createControlsDropdown() {
                const card = el.closest('.card');
                if (!card) return;

                const wrapper = document.createElement('div');
                wrapper.className = 'd-flex gap-2 mb-2 chart-controls';
                wrapper.style.alignItems = 'center';

                wrapper.innerHTML = `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="viewDropdownBtn" data-bs-toggle="dropdown">
                                Pilih: <span id="viewDropdownLabel">Bulanan</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item view-select" data-mode="daily" href="#">Harian</a></li>
                                <li><a class="dropdown-item view-select" data-mode="weekly" href="#">Mingguan</a></li>
                                <li><a class="dropdown-item view-select" data-mode="monthly" href="#">Bulanan</a></li>
                            </ul>
                        </div>
                        <div class="ms-auto text-muted small align-self-center">
                            Menampilkan: <span id="currentModeLabel"></span>
                        </div>
                    `;

                const chartCompact = card.querySelector('.chart-compact');
                card.insertBefore(wrapper, chartCompact);

                wrapper.querySelectorAll('.view-select').forEach(item => {
                    item.addEventListener('click', ev => {
                        ev.preventDefault();
                        updateChartDropdown(item.dataset.mode);
                    });
                });

                const initLabel = initialView === 'daily' ? 'Harian'
                    : initialView === 'weekly' ? 'Mingguan'
                        : 'Bulanan';

                wrapper.querySelector('#viewDropdownLabel').textContent = initLabel;
            })();

            function updateChartDropdown(mode) {
                const d = datasets[mode];
                chart.data.labels = d.labels;
                chart.data.datasets[0].data = d.data;
                chart.update();

                const l1 = document.getElementById('currentModeLabel');
                const l2 = document.getElementById('viewDropdownLabel');

                const txt = mode === 'daily' ? 'Harian' :
                    mode === 'weekly' ? 'Mingguan' : 'Bulanan';

                if (l1) l1.textContent = txt;
                if (l2) l2.textContent = txt;
            }

            updateChartDropdown(initialView);
        })();
    </script>


    {{-- =============== REALTIME USER ONLINE ================= --}}
    <script>
        (function () {
            const el = document.getElementById('onlineCount');
            if (!el) return;

            const url = "{{ route('admin.online-users-count') }}";

            async function refreshOnline() {
                try {
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.online !== undefined) {
                        el.textContent = data.online;
                    }
                } catch (e) { }
            }

            refreshOnline();
            setInterval(refreshOnline, 5000);
        })();
    </script>
@endpush