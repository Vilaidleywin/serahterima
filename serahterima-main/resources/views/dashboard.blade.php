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
        ['label'=>'Total Dokumen', 'value'=>$total,   'url'=>route('documents.index')],
        ['label'=>'Menunggu Persetujuan','value'=>$pending,'url'=>route('documents.index',['status'=>'PENDING'])],
        ['label'=>'Selesai', 'value'=>$done, 'url'=>route('documents.index',['status'=>'DONE'])],
        ['label'=>'Gagal',   'value'=>$failed, 'url'=>route('documents.index',['status'=>'FAILED'])],
      ];
    @endphp
    @foreach($tiles as $t)
      <div class="col-lg-3 col-md-6">
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
          <a href="{{ route('documents.index',['status'=>'PENDING']) }}" class="btn btn-sm btn-ghost">Pending</a>
          <a href="{{ route('documents.index',['status'=>'DONE']) }}" class="btn btn-sm btn-ghost">Done</a>
          <a href="{{ route('documents.index',['status'=>'FAILED']) }}" class="btn btn-sm btn-ghost">Failed</a>
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
      <a href="{{ route('documents.index',['status'=>'PENDING']) }}" class="text-decoration-none">
        <div class="card-soft p-3">
          <div class="text-muted small">Pending &gt; {{ $overdueDays }} hari</div>
          <div class="fs-4 fw-semibold {{ $pendingOverdue? 'text-danger':'text-success' }}">{{ $pendingOverdue }}</div>
        </div>
      </a>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Line chart (12 bulan)
      const lineCtx = document.getElementById('lineChart');
      if (lineCtx && typeof Chart !== 'undefined') {
        new Chart(lineCtx, {
          type: 'line',
          data: {
            labels: @json($lineLabels),
            datasets: [{
              data: @json($lineData),
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
            plugins: { legend:{display:false}, tooltip:{mode:'index', intersect:false} },
            scales: { x:{ grid:{display:false} }, y:{ beginAtZero:true, ticks:{precision:0} } }
          }
        });
      }

      // Bar chart (bulan ini)
      const barCtx = document.getElementById('barChart');
      if (barCtx && typeof Chart !== 'undefined') {
        const labels = @json($barLabels);
        const data   = @json($barData);
        new Chart(barCtx, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              data,
              backgroundColor: ['#FACC15CC', '#22C55ECC', '#EF4444CC'],
              borderColor:     ['#FACC15',   '#22C55E',   '#EF4444'  ],
              borderWidth: 2,
              borderRadius: 10,
              hoverBorderWidth: 3
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: { display:false },
              datalabels: {
                color:'#111827', anchor:'end', align:'start',
                font:{weight:'600'}, formatter:v => v>0 ? v : ''
              },
              tooltip: {
                backgroundColor:'#1F2937', titleColor:'#fff', bodyColor:'#fff', displayColors:false,
                callbacks: { label: ctx => `${ctx.parsed.y} dokumen` }
              }
            },
            onClick: (e, els) => {
              if (els.length) {
                const status = labels[els[0].index];
                window.location.href = `{{ route('documents.index') }}?status=${status}`;
              }
            },
            scales: {
              x: { grid:{display:false}, ticks:{ font:{weight:'600'} } },
              y: { beginAtZero:true, ticks:{precision:0, stepSize:1}, grid:{color:'rgba(209,213,219,.35)'} }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
          },
          plugins:[ChartDataLabels]
        });
      }

      // Donut chart (share status)
      const donutCtx = document.getElementById('donutChart');
      if (donutCtx && typeof Chart !== 'undefined') {
        new Chart(donutCtx, {
          type:'doughnut',
          data:{
            labels: @json($donut['labels']),
            datasets:[{ data:@json($donut['data']),
              backgroundColor:['#FACC15','#22C55E','#EF4444'] }]
          },
          options:{ plugins:{ legend:{ position:'bottom' } }, cutout:'60%' }
        });
      }
    });
  </script>
@endpush
