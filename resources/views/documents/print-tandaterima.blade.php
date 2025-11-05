<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tanda Terima – {{ $document->number }}</title>
<style>
  :root{
    --page-w: 210mm;      /* A4 */
    --page-h: 297mm;
    --m-top: 45mm;        /* ruang untuk kop saat print */
    --m-btm: 22mm;        /* ruang untuk footer saat print */
  }

  /* ====== SCREEN PREVIEW ====== */
  @media screen {
    body{ background:#f2f4f7; margin:0; }
    .page{
      width: var(--page-w);
      min-height: var(--page-h);
      margin: 24px auto;
      background:#fff;
      box-shadow: 0 6px 24px rgba(0,0,0,.08);
      padding: 24mm 18mm;              /* nyaman saat dibaca di layar */
    }
    .kop, .footer{
      position: static;                 /* TIDAK fixed di screen */
    }
    .kop img { width: 100%; height: auto; }
    .kop-flex {
      display:flex; align-items:center; justify-content:space-between;
      gap: 12mm; margin-bottom: 10mm;
    }
    .kop-left, .kop-right { height: 22mm; }
    .kop-left img, .kop-right img { height: 100%; width:auto; object-fit:contain; }
  }

  /* ====== PRINT ====== */
  @page{ size: A4; margin: 0; }
  @media print {
    html, body{ width: var(--page-w); height: var(--page-h); }
    .page{
      width: var(--page-w);
      min-height: var(--page-h);
      padding: 0;                        /* margin diatur manual */
    }
    .kop{
      position: fixed; top: 12mm; left: 18mm; right: 18mm;
    }
    .footer{
      position: fixed; bottom: 10mm; left:0; right:0; text-align:center; font-size:9pt;
    }
    .content{
      /* beri ruang supaya tidak ketimpa kop/footer */
      margin: var(--m-top) 18mm var(--m-btm);
      font-size: 12pt;
      line-height: 1.5;
    }
    .kop-flex { display:flex; align-items:center; justify-content:space-between; }
    .kop-left, .kop-right { height: 20mm; }
    .kop-left img, .kop-right img { height: 100%; width:auto; object-fit:contain; }
  }

  /* ====== Umum ====== */
  h2{ text-align:center; text-decoration:underline; margin: 0 0 12mm; }
  table{ width:100%; font-size:12pt; line-height:1.6; }
  td:first-child{ width:34%; vertical-align:top; }
  td:last-child{ padding-left:6px; }
  ul{ margin: 4mm 0 0 8mm; }
  .ttd{ margin-top: 24mm; text-align:right; font-size:12pt; }
</style>
</head>
<body>

<div class="page">
  <!-- KOP: pilih SALAH SATU blok -->
  <!-- Opsi A: satu gambar kop lebar -->
  {{-- <div class="kop"><img src="{{ asset('storage/kopsurat.png') }}" alt="Kop"></div> --}}

  <!-- Opsi B: dua logo kiri–kanan -->
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
    <h2>CETAK TANDA TERIMA SURAT</h2>

    <table>
      <tr>
        <td>Telah terima dari</td>
        <td>: {{ $document->sender ?? '-' }}</td>
      </tr>
      <tr>
        <td>Ditujukan kepada</td>
        <td>: {{ $document->receiver ?? '-' }} @if($document->division) ({{ $document->division }}) @endif</td>
      </tr>
      <tr>
        <td>Hari/Tanggal</td>
        <td>: {{ $document->date?->translatedFormat('l, d F Y') ?? '-' }}</td>
      </tr>
    </table>

    <p style="margin-top:8mm;"><strong>Berupa:</strong></p>
    <ul>
      <li>{{ $document->title }}</li>
      @if($document->description)
        <li>{{ $document->description }}</li>
      @endif
    </ul>

    <div class="ttd">
      <p>Penerima,</p>
      <br><br><br><br>
      <p>( ........................................................ )</p>
    </div>
  </div>

  <div class="footer">
    Gedung Pelni Kemayoran - Jakarta Pusat | Telp: (021) 42883720 - 42883749 |
    Email: corporate@pidc.co.id / pt.pidc@gmail.com | Website: pelniservices.com
  </div>
</div>

</body>
</html>
