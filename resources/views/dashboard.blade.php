@extends('layouts.app')

@push('styles')
<style>
  /* ====== Global look mirip foto kedua ====== */
  body { background:#f3f4f6; }                 /* abu-abu lembut agar card keliatan */
  :root{
    --soft-bg:#ffffff; --soft-br:#e5e7eb;      /* gray-200 */
    --soft-shadow:0 8px 24px rgba(0,0,0,.06);
  }
  .card-soft{
    background:var(--soft-bg);
    border:1px solid var(--soft-br);
    border-radius:16px;
    box-shadow:var(--soft-shadow);
  }
  .card-soft.sm{ border-radius:14px; box-shadow:0 6px 18px rgba(0,0,0,.05); }
  .stat-label{font-size:.82rem;color:#6b7280}
  .stat-value{font-size:2rem;font-weight:700;line-height:1.1}
  .pill{border:none; padding:.35rem .6rem; border-radius:.5rem; font-weight:600}
  .pill-gray{background:#6B7280; color:#fff}
  .pill-green{background:#22C55E; color:#fff}
  .pill-red{background:#EF4444; color:#fff}
</style>
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Dashboard</h2>
      <div class="text-muted">Ringkasan dokumen</div>
    </div>
  </div>

  {{-- Row 1: Ringkasan (card besar) --}}
  <div class="row g-3 mb-3">
    @php
      // Tiga tile besar seperti di foto kedua.
      $tiles = [
        ['label' => 'Total Dokumen', 'value' => $total, 'url' => route('documents.index'), 'color' => 'text-primary'],
        ['label' => 'SUBMITTED', 'value' => $submitted, 'url' => route('documents.index', ['status' => 'SUBMITTED']), 'color' => 'text-success'],
        ['label' => 'REJECTED', 'value' => $rejected, 'url' => route('documents.index', ['status' => 'REJECTED']), 'color' => 'text-danger'],
      ];
    @endphp
    @foreach($tiles as $t)
      <div class="col-lg-4 col-md-6">
        <a href="{{ $t['url'] }}" class="text-decoration-none">
          <div class="card-soft p-3 h-100">
            <div class="stat-label">{{ $t['label'] }}</div>
            <div class="stat-value {{ $t['color'] }}">{{ $t['value'] }}</div>
          </div>
        </a>
      </div>
    @endforeach
  </div>

  {{-- Row 2: Charts (dua card) --}}
  <div class="row g-3 mb-3">
    <div class="col-lg-7">
      <div class="card-soft p-3 h-100">
        <div class="fw-semibold mb-2">Evolusi Dokumen (12 bulan)</div>
        <div style="height:300px"><canvas id="lineChart"></canvas></div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card-soft p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Distribusi Status (bulan ini)</div>
        </div>
        <div style="height:220px"><canvas id="barChart"></canvas></div>
        <div class="d-flex gap-2 mt-2 flex-wrap">
          <a href="{{ route('documents.index', ['status' => 'SUBMITTED']) }}" class="pill pill-green">SUBMITTED</a>
          <a href="{{ route('documents.index', ['status' => 'REJECTED']) }}" class="pill pill-red">REJECTED</a>
          <a href="{{ route('documents.index', ['status' => 'DRAFT']) }}" class="pill pill-gray">DRAFT</a>
        </div>
      </div>

      <div class="card-soft p-3">
        <div class="fw-semibold mb-2">Share Status (total)</div>
        <div style="height:200px; position:relative">
          <canvas id="donutChart"></canvas>
          <!-- angka total di tengah donut -->
          <div id="donutCenter"
               style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-weight:700;"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Row 3: KPI mini (empat card kecil) --}}
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
          <div class="stat-label">SUBMITTED &gt; {{ $overdueDays }} hari</div>
          <div class="stat-value {{ ($submittedOverdue ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
            {{ $submittedOverdue }}
          </div>
        </div>
      </a>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <script>
    const arr = (v) => Array.isArray(v) ? v : (v == null ? [] : [v]);

    function gradient(ctx, rgba){
      const g = ctx.createLinearGradient(0,0,0,300);
      g.addColorStop(0, rgba.replace('1)', '.2)'));
      g.addColorStop(1, rgba.replace('1)', '0)'));
      return g;
    }

    function initCharts() {
      if (typeof Chart === 'undefined') return;
      if (typeof ChartDataLabels !== 'undefined' && !Chart.registry.plugins.get('datalabels')) {
        Chart.register(ChartDataLabels);
      }

      // ===== LINE (area) =====
      const lineEl = document.getElementById('lineChart');
      if (lineEl) {
        const labels = @json($lineLabels ?? []);
        const data = @json($lineData ?? []);
        const ctx = lineEl.getContext('2d');
        new Chart(lineEl, {
          type: 'line',
          data: {
            labels: arr(labels),
            datasets: [{
              data: arr(data),
              borderColor: 'rgba(46,90,172,1)',
              backgroundColor: gradient(ctx,'rgba(46,90,172,1)'),
              fill: true, borderWidth: 2, tension: .35, pointRadius: 2
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } }
          }
        });
      }

      // ===== BAR (bulan ini) =====
      const barEl = document.getElementById('barChart');
      if (barEl) {
        const labels = @json($barLabels ?? []);
        const data = @json($barData ?? []);
        new Chart(barEl, {
          type: 'bar',
          data: {
            labels: arr(labels),
            datasets: [{
              data: arr(data),
              backgroundColor: ['#22C55ECC', '#EF4444CC', '#6B7280CC'], /* urutan: SUBMITTED, REJECTED, DRAFT */
              borderColor: ['#22C55E', '#EF4444', '#6B7280'],
              borderWidth: 2, borderRadius: 10, hoverBorderWidth: 3
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              datalabels: {
                color: '#111827',
                anchor: 'end', align: 'start',
                font: { weight: '600' },
                formatter: v => (typeof v === 'number' && v > 0) ? v : ''
              }
            },
            onClick: (e, els) => {
              if (els.length) {
                const idx = els[0].index;
                const status = arr(labels)[idx];
                if (status) {
                  const base = `{{ route('documents.index') }}`;
                  window.location.href = `${base}?status=${encodeURIComponent(status)}`;
                }
              }
            },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } } }
          }
        });
      }

      // ===== DONUT (share status total) =====
      const donutEl = document.getElementById('donutChart');
      if (donutEl) {
        const labels = @json(($donut['labels'] ?? []) ?: []);
        const data = @json(($donut['data'] ?? []) ?: []);
        const total = arr(data).reduce((a,b)=>a+(+b||0),0);
        const center = document.getElementById('donutCenter');
        if (center) center.textContent = total;

        new Chart(donutEl, {
          type: 'doughnut',
          data: {
            labels: arr(labels),
            datasets: [{
              data: arr(data),
              backgroundColor: ['#22C55ECC', '#EF4444CC', '#6B7280CC'] /* SUBMITTED, REJECTED, DRAFT */
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            cutout: '60%'
          }
        });
      }
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initCharts, { once: true });
    } else { initCharts(); }
  </script>
@endpush
