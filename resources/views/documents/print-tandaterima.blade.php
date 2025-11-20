<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tanda Terima – {{ $document->number }}</title>

  {{-- kalau punya CSS eksternal boleh tetap, tapi style di bawah akan override --}}
  {{-- <link rel="stylesheet" href="{{ asset('css/print-tandaterima.css') }}"> --}}

  <style>
    @page {
      size: A4;
      margin: 10mm 15mm; /* margin aman supaya tidak kepotong printer */
    }

    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #fff;
    }

    * {
      box-sizing: border-box;
    }

    .page {
      width: 100%;
      /* tinggi dibiarkan otomatis, jangan di-fix 297mm */
    }

    /* KOP */
    .kop {
      margin-bottom: 12mm;
    }

    .kop-flex {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .kop-left img,
    .kop-right img {
      max-height: 55px;
      height: auto;
    }

    /* KONTEN UTAMA */
    .content {
      padding: 0 5mm 0 5mm;
    }

    .content h2 {
      text-align: center;
      font-size: 16pt;
      margin: 0 0 10mm;
      text-decoration: underline;
    }

    .content table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11pt;
    }

    .content table td {
      padding: 2px 0;
      vertical-align: top;
    }

    .content table td:first-child {
      width: 30%;
    }

    .content table td:last-child {
      width: 70%;
    }

    /* TANDA TANGAN */
    .ttd-single {
      margin-top: 15mm; /* kalau masih kepotong, kecilin lagi misal 10mm */
      display: flex;
      justify-content: center;
    }

    .ttd-col {
      text-align: center;
    }

    .ttd-role {
      margin-bottom: 18px;
      font-size: 11pt;
    }

    .ttd-sign {
      height: 50px;
      margin-bottom: 8px;
    }

    .ttd-sign img {
      max-height: 100%;
      max-width: 100%;
    }

    .ttd-name {
      font-size: 11pt;
    }

    /* FOOTER */
    .footer-img {
      margin-top: 12mm;
      padding: 0 5mm 0 5mm;
    }

    .footer-img img {
      width: 100%;
      height: auto;
    }
  </style>
</head>
<body>

<div class="page">
  <!-- KOP -->
  <div class="kop">
    <div class="kop-flex">
      <div class="kop-left">
        <img src="{{ asset('storage/akhlak.png') }}" alt="AKHLAK">
      </div>
      <div class="kop-right">
        <img src="{{ asset('storage/pelni.png') }}" alt="PELNI SERVICES">
      </div>
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
      <tr>
        <td>Nominal</td>
        <td>:
          @if(!is_null($document->amount_idr))
            Rp {{ number_format((int) $document->amount_idr, 0, ',', '.') }}
          @else
            -
          @endif
        </td>
      </tr>
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
  