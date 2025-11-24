<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tanda Terima – {{ $document->number }}</title>

  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 11pt;
      background: #fff;
    }

    /* === SETTING CETAK A4 FIX === */
    @page {
      size: A4 portrait;
      margin: 0; /* full kontrol dari CSS sendiri */
    }

    @media print {
      html, body {
        background: #fff;
      }

      .page {
        box-shadow: none !important;
        margin: 0;
      }
    }

    /* === HALAMAN FULL A4 === */
    .page {
      width: 210mm;
      height: 297mm;
      margin: 0 auto;
      background: #fff;
      position: relative;
      overflow: hidden;
      box-shadow: 0 0 6px rgba(0,0,0,0.15); /* cuma keliatan di layar */
    }

    /* padding dalam = "margin" konten dokumen */
    .page-inner {
      padding: 5mm 20mm 35mm 20mm; /* ATAS DIKURANGI → HEADER NAIK */
      height: 100%;
    }

    /* === KOP (HEADER) === */
    .kop {
      margin-bottom: 6mm;
    }

    .kop-flex {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .kop-left img {
      height: 45px;
      display: block;
    }

    .kop-right {
      text-align: right;
    }

    .kop-right img {
      height: 45px;
      display: block;
    }

    .kop-line {
      margin-top: 4mm;
      border-bottom: 2px solid #000;
    }

    /* === JUDUL === */
    .judul-wrapper {
      text-align: center;
      margin-top: 8mm;
      margin-bottom: 10mm;
    }

    .judul-wrapper h2 {
      margin: 0;
      font-size: 15pt;
      text-decoration: underline;
      font-weight: 700;
    }

    .judul-wrapper .nomor {
      margin-top: 3mm;
      font-size: 11pt;
    }

    /* === TABEL INFO === */
    .info-section {
      margin-top: 0;
    }

    .info-table {
      width: 100%;
      border-collapse: collapse;
    }

    .info-table td {
      padding: 4px 0;
      vertical-align: top;
      font-size: 11pt;
    }

    .info-label {
      width: 25%;
    }

    .info-colon {
      width: 3%;
      text-align: center;
    }

    .info-value {
      width: 72%;
    }

    .with-line {
      border-bottom: 1px solid #000;
      padding-bottom: 2px;
      min-height: 16px;
    }

    .with-line--multiline {
      min-height: 18px;
    }

    /* === TANDA TANGAN DI BAWAH === */
    .ttd-wrapper {
      position: absolute;
      right: 20mm;        /* sejajar padding kanan */
      bottom: 40mm;       /* jarak dari footer */
      text-align: center;
    }

    .ttd-block {
      width: 60mm;
      font-size: 11pt;
    }

    .ttd-role {
      margin-bottom: 15px;
    }

    .ttd-sign {
      height: 55px;
      margin-bottom: 6px;
    }

    .ttd-sign img {
      max-height: 100%;
      max-width: 100%;
      display: block;
      margin: 0 auto;
    }

    .ttd-name {
      margin-top: 2px;
    }

    /* === FOOTER GAMBAR NEMPEL PALING BAWAH === */
    .footer-img {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
    }

    .footer-img img {
      width: 100%;
      height: auto;
      display: block;
    }
  </style>
</head>
<body>

<div class="page">
  <div class="page-inner">

    <!-- KOP -->
    <div class="kop">
      <div class="kop-flex">
        <div class="kop-left">
          <img src="{{ asset('storage/akhlak.png') }}" alt="AKHLAK">
        </div>
        <div class="kop-right">
          <img src="{{ asset('storage/pelni.png') }}" alt="PELNI">
        </div>
      </div>
      <div class="kop-line"></div>
    </div>

    <!-- JUDUL -->
    <div class="judul-wrapper">
      <h2>SURAT SERAH TERIMA DOKUMEN</h2>
      <div class="nomor">
        Nomor: {{ $document->number ?? '...............................' }}
      </div>
    </div>

    <!-- INFO -->
    <div class="info-section">
      <table class="info-table">
        <tr>
          <td class="info-label">Judul Dokumen</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line with-line--multiline">
            {{ $document->title ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">No Dokumen</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line">
            {{ $document->number ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">Tanggal</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line">
            {{ $document->date?->translatedFormat('l, d F Y') ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">Divisi</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line">
            {{ $document->division ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">Pengirim</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line">
            {{ $document->sender ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">Penerima</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line">
            {{ $document->receiver ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">Nominal</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line">
            @if(!is_null($document->amount_idr))
              Rp {{ number_format((int) $document->amount_idr, 0, ',', '.') }}
            @else
              &nbsp;
            @endif
          </td>
        </tr>
        <tr>
          <td class="info-label">Tujuan</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line with-line--multiline">
            {{ $document->destination ?? ' ' }}
          </td>
        </tr>
        <tr>
          <td class="info-label">Catatan</td>
          <td class="info-colon">:</td>
          <td class="info-value with-line with-line--multiline">
            {{ $document->description ?? ' ' }}
          </td>
        </tr>
      </table>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-wrapper">
      <div class="ttd-block">
        <div class="ttd-role">Penerima,</div>
        <div class="ttd-sign">
          @if(!empty($document->signature_path))
            <img src="{{ asset('storage/'.$document->signature_path) }}" alt="Tanda Tangan Penerima">
          @endif
        </div>
        <div class="ttd-name">
          ( {{ $document->receiver ?: '..............................' }} )
        </div>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <div class="footer-img">
    <img src="{{ asset('storage/footer.png') }}" alt="Footer">
  </div>
</div>

</body>
</html>
