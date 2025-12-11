@extends('layouts.app')

@push('styles')
  <style>
    body {
      background: #f3f4f6;
    }

    :root {
      --soft-bg: #ffffff;
      --soft-br: #e5e7eb;
      --soft-shadow: 0 8px 24px rgba(0, 0, 0, .06);
    }

    .card-soft {
      background: var(--soft-bg);
      border: 1px solid var(--soft-br);
      border-radius: 16px;
      box-shadow: var(--soft-shadow);
    }

    .card-soft.sm {
      border-radius: 14px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
    }

    .stat-label {
      font-size: .82rem;
      color: #6b7280;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1.1;
    }

    .pill {
      border: none;
      padding: .35rem .6rem;
      border-radius: .5rem;
      font-weight: 600;
    }

    .pill-gray {
      background: #6B7280;
      color: #fff;
    }

    .pill-green {
      background: #22C55E;
      color: #fff;
    }

    .pill-red {
      background: #EF4444;
      color: #fff;
    }

    #lineChart,
    #barChart,
    #donutChart {
      cursor: pointer;
    }
  </style>
@endpush



@section('content')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Dashboard</h2>
      <div class="text-muted">Ringkasan dokumen</div>
    </div>
  </div>

  {{-- ROW 1 --}}
  <div class="row g-3 mb-3">
    @php
      $draftSafe = $draft ?? max(0, $total - $submitted - $rejected);

      $tiles = [
        ['label' => 'Total Dokumen', 'value' => $total, 'url' => route('documents.index'), 'color' => 'text-primary'],
        ['label' => 'SUBMITTED', 'value' => $submitted, 'url' => route('documents.index', ['status' => 'SUBMITTED']), 'color' => 'text-success'],
        ['label' => 'REJECTED', 'value' => $rejected, 'url' => route('documents.index', ['status' => 'REJECTED']), 'color' => 'text-danger'],
        ['label' => 'DRAFT', 'value' => $draftSafe, 'url' => route('documents.index', ['status' => 'DRAFT']), 'color' => 'text-secondary'],
      ];
    @endphp

    @foreach($tiles as $t)
      <div class="col-lg-3 col-md-6">
        <a href="{{ $t['url'] }}" class="text-decoration-none">
          <div class="card-soft p-3 h-100">
            <div class="stat-label">{{ $t['label'] }}</div>
            <div class="stat-value {{ $t['color'] }}">{{ $t['value'] }}</div>
          </div>
        </a>
      </div>
    @endforeach
  </div>

  {{-- ROW 2 --}}
  <div class="row g-3 mb-3">
    <div class="col-lg-7">
      <div class="card-soft p-3 h-100">

        <div class="fw-semibold mb-2">Evolusi Dokumen</div>

        <div class="dropdown mb-2">
          <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Pilih: <span id="viewDropdownLabel">Bulanan</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item view-select" data-mode="daily" href="#">Harian</a></li>
            <li><a class="dropdown-item view-select" data-mode="weekly" href="#">Mingguan</a></li>
            <li><a class="dropdown-item view-select" data-mode="monthly" href="#">Bulanan</a></li>
          </ul>
        </div>

        <div class="text-muted small mb-2">Menampilkan: <span id="currentModeLabel">Bulanan</span></div>

        <div style="height:300px">
          <canvas id="lineChart"></canvas>
        </div>

      </div>
    </div>

    <div class="col-lg-5">
      {{-- BAR CHART --}}
      <div class="card-soft p-3 mb-3">
        <div class="fw-semibold mb-2">Distribusi Status (bulan ini)</div>
        <div style="height:220px"><canvas id="barChart"></canvas></div>

        <div class="d-flex gap-2 mt-2 flex-wrap">
          <a href="{{ route('documents.index', ['status' => 'SUBMITTED']) }}" class="pill pill-green">SUBMITTED</a>
          <a href="{{ route('documents.index', ['status' => 'REJECTED']) }}" class="pill pill-red">REJECTED</a>
          <a href="{{ route('documents.index', ['status' => 'DRAFT']) }}" class="pill pill-gray">DRAFT</a>
        </div>
      </div>

      {{-- DONUT --}}
      <div class="card-soft p-3">
        <div class="fw-semibold mb-2">Share Status (total)</div>
        <div style="height:200px; position:relative">
          <canvas id="donutChart"></canvas>
        </div>
      </div>
    </div>
  </div>



  {{-- ROW 3 --}}
  <div class="row g-3">
    <div class="col-md-3">
      <a href="{{ route('documents.index', ['period' => 'today']) }}" class="text-decoration-none">
        <div class="card-soft sm p-3">
          <div class="stat-label">Dibuat hari ini</div>
          <div class="stat-value">{{ $createdToday }}</div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="{{ route('documents.index', ['period' => 'week']) }}" class="text-decoration-none">
        <div class="card-soft sm p-3">
          <div class="stat-label">Dibuat minggu ini</div>
          <div class="stat-value">{{ $createdWeek }}</div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="{{ route('documents.index', ['period' => 'month']) }}" class="text-decoration-none">
        <div class="card-soft sm p-3">
          <div class="stat-label">Dibuat bulan ini</div>
          <div class="stat-value">{{ $createdMonth }}</div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="{{ route('documents.index', ['status' => 'SUBMITTED']) }}" class="text-decoration-none">
        <div class="card-soft sm p-3">
          <div class="stat-label">SUBMITTED > {{ $overdueDays }} hari</div>
          <div class="stat-value {{ $submittedOverdue > 0 ? 'text-danger' : 'text-success' }}">{{ $submittedOverdue }}</div>
        </div>
      </a>
    </div>
  </div>

@endsection



@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

  <script>

    function gradient(ctx, rgba) {
      const g = ctx.createLinearGradient(0, 0, 0, 300);
      g.addColorStop(0, rgba.replace('1)', '.2)'));
      g.addColorStop(1, rgba.replace('1)', '0)'));
      return g;
    }

    function initCharts() {

      /* ===========================================================
         PREPARE DATA DAILY / WEEKLY / MONTHLY (from Controller)
      =========================================================== */
      window.lineLabels_daily = @json($lineLabelsDaily);
      window.lineData_daily = @json($lineDataDaily);
      window.lineRanges_daily = @json($lineRangesDaily);

      window.lineLabels_weekly = @json($lineLabelsWeekly);
      window.lineData_weekly = @json($lineDataWeekly);
      window.lineRanges_weekly = @json($lineRangesWeekly);

      window.lineLabels_monthly = @json($lineLabelsMonthly);
      window.lineData_monthly = @json($lineDataMonthly);
      window.lineRanges_monthly = @json($lineRangesMonthly);


      /* ===========================================================
         LINE CHART
      =========================================================== */
      const lineEl = document.getElementById('lineChart');

      if (lineEl) {

        const mode = @json($initialView ?? 'monthly');

        let labels = window[`lineLabels_${mode}`];
        let data = window[`lineData_${mode}`];
        let ranges = window[`lineRanges_${mode}`];

        const ctx = lineEl.getContext('2d');

        const chart = new Chart(lineEl, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              data,
              borderColor: 'rgba(46,90,172,1)',
              backgroundColor: gradient(ctx, 'rgba(46,90,172,1)'),
              fill: true,
              borderWidth: 2,
              tension: .35,
              pointRadius: 3
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true } },
            onHover(evt) {
              const pts = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: false }, true);
              evt.native.target.style.cursor = pts.length ? 'pointer' : 'default';
            }
          }
        });

        /* CLICK — redirect */
        lineEl.addEventListener('click', function (evt) {
          const pts = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: false }, true);
          if (!pts.length) return;

          const idx = pts[0].index;
          const r = ranges[idx];
          if (!r) return;

          const base = `{{ route('documents.index') }}`;
          window.location.href = `${base}?date_from=${r.start}&date_to=${r.end}`;
        });

        /* SWITCH MODE (dropdown) */
        document.querySelectorAll('.view-select').forEach(el => {
          el.addEventListener('click', e => {
            e.preventDefault();

            const selected = el.dataset.mode;

            labels = window[`lineLabels_${selected}`];
            data = window[`lineData_${selected}`];
            ranges = window[`lineRanges_${selected}`];

            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.update();

            document.getElementById('viewDropdownLabel').textContent =
              selected === 'daily' ? 'Harian' :
                selected === 'weekly' ? 'Mingguan' : 'Bulanan';

            document.getElementById('currentModeLabel').textContent =
              selected === 'daily' ? 'Harian' :
                selected === 'weekly' ? 'Mingguan' : 'Bulanan';
          });
        });

      }


      /* ===========================================================
         BAR & DONUT — versi kamu, tidak saya ubah
      =========================================================== */

      const barEl = document.getElementById('barChart');
      if (barEl) {
        const rawLabels = @json($barLabels ?? []);
        const rawData = @json($barData ?? []);
        const want = ['SUBMITTED', 'REJECTED', 'DRAFT'];

        const map = {};
        rawLabels.forEach((l, i) => map[l] = Number(rawData[i] || 0));

        const labels = want;
        const data = labels.map(l => map[l] ?? 0);

        const pal = {
          SUBMITTED: { bg: '#22C55ECC', border: '#22C55E' },
          REJECTED: { bg: '#EF4444CC', border: '#EF4444' },
          DRAFT: { bg: '#6B7280CC', border: '#6B7280' },
        };

        new Chart(barEl, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              data,
              backgroundColor: labels.map(l => pal[l].bg),
              borderColor: labels.map(l => pal[l].border),
              borderWidth: 2,
              borderRadius: 10
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            onClick(e, els) {
              if (!els.length) return;
              const status = labels[els[0].index];
              const base = `{{ route('documents.index') }}`;
              window.location.href = `${base}?status=${encodeURIComponent(status)}`;
            }
          }
        });

      }


      const donutEl = document.getElementById('donutChart');
      if (donutEl) {

        const rawLabels = @json($donut['labels'] ?? []);
        const rawData = @json($donut['data'] ?? []);

        const norm = s => (s || '').trim().toUpperCase();

        const map = {};
        rawLabels.forEach((l, i) => map[norm(l)] = Number(rawData[i] || 0));

        const labels = ['SUBMITTED', 'REJECTED', 'DRAFT'];
        const data = labels.map(l => map[norm(l)] ?? 0);

        new Chart(donutEl, {
          type: 'doughnut',
          data: {
            labels,
            datasets: [{
              data,
              backgroundColor: ['#22C55ECC', '#EF4444CC', '#6B7280CC'],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            cutout: '70%'
          }
        });

      }

    }



    document.readyState === "loading"
      ? document.addEventListener("DOMContentLoaded", initCharts)
      : initCharts();

  </script>
@endpush