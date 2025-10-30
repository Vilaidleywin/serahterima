@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Dashboard</h2>
      <div class="text-muted">Ringkasan dokumen</div>
    </div>
  </div>

  {{-- Row 1: Ringkasan --}}
  <div class="row g-3 mb-3">
    @php
      $tiles = [
        ['label' => 'Total Dokumen', 'value' => $total, 'url' => route('documents.index')],
        ['label' => 'SUBMITTED', 'value' => $submitted, 'url' => route('documents.index', ['status' => 'SUBMITTED'])],
        ['label' => 'REJECTED', 'value' => $rejected, 'url' => route('documents.index', ['status' => 'REJECTED'])],
      ];
    @endphp
    @foreach($tiles as $t)
      <div class="col-lg-4 col-md-6">
        <a href="{{ $t['url'] }}" class="text-decoration-none">
          <div class="card-soft p-3 h-100">
            <div class="text-muted small">{{ $t['label'] }}</div>
            <div class="fs-3 fw-semibold text-dark">{{ $t['value'] }}</div>
          </div>
        </a>
      </div>
    @endforeach
  </div>

  {{-- Row 2: Charts --}}
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
          <a href="{{ route('documents.index', ['status' => 'SUBMITTED']) }}" class="btn btn-sm btn-ghost">SUBMITTED</a>
          <a href="{{ route('documents.index', ['status' => 'REJECTED']) }}" class="btn btn-sm btn-ghost">REJECTED</a>
        </div>
      </div>

      <div class="card-soft p-3">
        <div class="fw-semibold mb-2">Share Status (total)</div>
        <div style="height:200px"><canvas id="donutChart"></canvas></div>
      </div>
    </div>
  </div>

  {{-- Row 3: KPI mini --}}
  <div class="row g-3">
    <div class="col-md-3">
      <div class="card-soft p-3">
        <div class="text-muted small">Dibuat hari ini</div>
        <div class="fs-4 fw-semibold">{{ $createdToday }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card-soft p-3">
        <div class="text-muted small">Dibuat minggu ini</div>
        <div class="fs-4 fw-semibold">{{ $createdWeek }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card-soft p-3">
        <div class="text-muted small">Dibuat bulan ini</div>
        <div class="fs-4 fw-semibold">{{ $createdMonth }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <a href="{{ route('documents.index', ['status' => 'SUBMITTED']) }}" class="text-decoration-none">
        <div class="card-soft p-3">
          <div class="text-muted small">SUBMITTED &gt; {{ $overdueDays }} hari</div>
          <div class="fs-4 fw-semibold {{ ($submittedOverdue ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
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
    // --- Helper: safe get arrays ---
    const arr = (v) => Array.isArray(v) ? v : (v == null ? [] : [v]);

    function initCharts() {
      if (typeof Chart === 'undefined') return;

      // Register plugin sekali untuk Chart.js v4 (hindari error plugin)
      if (typeof ChartDataLabels !== 'undefined' && !Chart.registry.plugins.get('datalabels')) {
        Chart.register(ChartDataLabels);
      }

      // LINE (12 bulan)
      const lineEl = document.getElementById('lineChart');
      if (lineEl) {
        const lineLabels = @json($lineLabels ?? []);
        const lineData = @json($lineData ?? []);
        new Chart(lineEl, {
          type: 'line',
          data: {
            labels: arr(lineLabels),
            datasets: [{
              data: arr(lineData),
              borderColor: '#2e5aac',
              backgroundColor: 'rgba(46,90,172,0.10)',
              fill: true,
              borderWidth: 2,
              tension: .35,
              pointRadius: 2
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } }
          }
        });
      }

      // BAR (bulan ini)
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
              backgroundColor: ['#22C55ECC', '#EF4444CC'],
              borderColor: ['#22C55E', '#EF4444'],

              borderWidth: 2,
              borderRadius: 10,
              hoverBorderWidth: 3
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              datalabels: {
                color: '#111827', anchor: 'end', align: 'start',
                font: { weight: '600' },
                formatter: v => (typeof v === 'number' && v > 0) ? v : ''
              },
              tooltip: {
                backgroundColor: '#1F2937', titleColor: '#fff', bodyColor: '#fff', displayColors: false,
                callbacks: { label: ctx => `${ctx.parsed.y} dokumen` }
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
            scales: {
              x: { grid: { display: false }, ticks: { font: { weight: '600' } } },
              y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 }, grid: { color: 'rgba(209,213,219,.35)' } }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
          }
        });
      }

      // DONUT (share status)
      const donutEl = document.getElementById('donutChart');
      if (donutEl) {
        const labels = @json(($donut['labels'] ?? []) ?: []);
        const data = @json(($donut['data'] ?? []) ?: []);
        new Chart(donutEl, {
          type: 'doughnut',
          data: {
            labels: arr(labels),
            datasets: [{
              data: arr(data),
              backgroundColor: ['#22C55ECC', '#EF4444CC'],
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

    // Panggil langsung kalau DOM sudah siap; kalau belum, tunggu
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initCharts, { once: true });
    } else {
      initCharts();
    }
  </script>
@endpush