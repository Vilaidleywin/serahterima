<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Surat Serah Terima Dokumen – {{ $document->number }}</title>
  <link rel="stylesheet" href="{{ asset('css/print-tandaterima.css') }}">
</head>

<body>

  @php
    // Pastikan $docDate adalah instance Carbon
    $docDate = $document->date instanceof \Carbon\Carbon
      ? $document->date
      : \Carbon\Carbon::parse($document->date ?? now());
  @endphp

  <div class="page">

    <!-- ======================= -->
    <!--           KOP           -->
    <!-- ======================= -->
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

    <!-- ======================= -->
    <!--         KONTEN          -->
    <!-- ======================= -->
    <div class="content">

      <h2>SURAT SERAH TERIMA DOKUMEN</h2>

      <p class="intro">
        Yang bertanda tangan di bawah ini menyatakan bahwa telah dilakukan serah terima dokumen
        dengan rincian sebagai berikut:
      </p>

      <!-- TABLE META -->
      <table class="meta-table">
        <tbody>
          <tr>
            <td class="label">Judul Dokumen</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->title ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">No Dokumen</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->number ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">
              {{ $docDate->translatedFormat('l, d F Y') }}
            </td>
          </tr>
          <tr>
            <td class="label">Divisi</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->division ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">Pengirim</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->sender ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">Penerima</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->receiver ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">Nominal</td>
            <td class="colon">:</td>
            <td class="value">
              @if ($document->amount_idr !== null)
                Rp. {{ number_format($document->amount_idr, 0, ',', '.') }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td class="label">Tujuan</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->destination ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">Catatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $document->description ?? '-' }}</td>
          </tr>
        </tbody>
      </table>

      <!-- ======================= -->
      <!--     BLOK TANDA TANGAN   -->
      <!-- ======================= -->
      <div class="ttd-single">
        <div class="ttd-col">

          <div class="ttd-place-date">
            {{ ($document->city ?? 'Jakarta') . ', ' . $docDate->translatedFormat('d F Y') }}
          </div>

          <div class="ttd-sign {{ empty($document->signature_path) ? 'ttd-sign--empty' : '' }}">
            @if (!empty($document->signature_path))
              <img src="{{ asset('storage/' . $document->signature_path) }}" alt="Tanda Tangan Penerima">
            @endif
          </div>


          <div class="ttd-name">
            {{ $document->receiver ?? '................................' }}
          </div>

          <div class="ttd-role-text">
            Penerima
          </div>

        </div>
      </div>

    </div>

    <!-- ======================= -->
    <!--        FOOTER           -->
    <!-- ======================= -->
    <div class="footer-img">
      <img src="{{ asset('storage/footer.png') }}" alt="Footer">
    </div>
  </div>

</body>

</html>