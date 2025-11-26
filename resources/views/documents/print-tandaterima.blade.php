<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tanda Terima – {{ $document->number }}</title>
  <link rel="stylesheet" href="{{ asset('css/print-tandaterima.css') }}">
  <style>
    @page { size: A4; margin: 0; }
    html, body {
      width: 210mm;
      height: 297mm;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    body { background: #fff; }
  </style>
</head>
<body>

<div class="page">
  <!-- KOP -->
  <div class="kop">
    <div class="kop-flex">
      <div class="kop-left"><img src="{{ asset('storage/akhlak.png') }}" alt="AKHLAK"></div>
      <div class="kop-right"><img src="{{ asset('storage/pelni.png') }}" alt="PELNI SERVICES"></div>
    </div>
  </div>

  <div class="content">
    <h2>SURAT SERAH TERIMA DOKUMEN</h2>
    <table>
      <tr><td>Judul Dokumen</td><td>: {{ $document->title ?? '-' }}</td></tr>
      <tr><td>No Dokumen</td><td>: {{ $document->number ?? '-' }}</td></tr>
      <tr><td>Tanggal</td><td>: {{ $document->date?->translatedFormat('l, d F Y') ?? '-' }}</td></tr>
      <tr><td>Divisi</td><td>: {{ $document->division ?? '-' }}</td></tr>
      <tr><td>Pengirim</td><td>: {{ $document->sender ?? '-' }}</td></tr>
      <tr><td>Penerima</td><td>: {{ $document->receiver ?? '-' }}</td></tr>
      <tr><td>Nominal</td><td>: {{ $document->amount_idr ?? '-' }}</td></tr>
      <tr><td>Tujuan</td><td>: {{ $document->destination ?? '-' }}</td></tr>
      <tr><td>Catatan</td><td>: {{ $document->description ?? '-' }}</td></tr>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-single">
      <div class="ttd-col">
        <div class="ttd-role">Penerima,</div>
        <div class="ttd-sign">
          @if(!empty($document->signature_path))
            <img src="{{ asset('storage/'.$document->signature_path) }}" alt="Tanda Tangan Penerima">
          @endif
        </div>
        <div class="ttd-name">
          ( {{ $document->receiver ?: '................................' }} )
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER IMAGE -->
  <div class="footer-img">
    <img src="{{ asset('storage/footer.png') }}" alt="Footer">
  </div>
</div>

</body>
</html>