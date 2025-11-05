@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-0">Ambil Foto Dokumen</h2>
            <div class="text-muted small">
                No. Dokumen: <strong>{{ $document->number }}</strong> — {{ $document->title }}
            </div>
        </div>
        <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-soft p-4">
        <div class="row g-4">
            {{-- ====== KAMERA ====== --}}
            <div class="col-lg-6">
                <div class="ratio ratio-4x3 bg-dark rounded overflow-hidden border">
                    <video id="cam" autoplay playsinline style="width:100%;height:100%;object-fit:cover;"></video>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button id="btnStart" type="button" class="btn btn-primary">
                        <i class="ti ti-camera"></i> Nyalakan Kamera
                    </button>
                    <button id="btnShot" type="button" class="btn btn-success" disabled>
                        <i class="ti ti-aperture"></i> Ambil Foto
                    </button>
                </div>

                <small class="text-muted d-block mt-2">
                    Pastikan kamu memberi izin kamera di browser.
                </small>
            </div>

            {{-- ====== HASIL FOTO ====== --}}
            <div class="col-lg-6">
                <div class="ratio ratio-4x3 border rounded bg-light d-flex align-items-center justify-content-center">
                    <canvas id="canvas" class="rounded"></canvas>
                </div>

                <form id="formPhoto" class="mt-3" method="POST" action="{{ route('documents.photo.store', $document) }}">
                    @csrf
                    <input type="hidden" name="photo" id="photoInput">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1" disabled id="btnSave">
                            <i class="ti ti-device-floppy"></i> Simpan Foto
                        </button>

                        <button type="button" class="btn btn-outline-danger" id="btnReset" disabled>
                            <i class="ti ti-refresh"></i> Ulangi
                        </button>
                    </div>
                </form>

                @error('photo')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cam = document.getElementById("cam");
            const btnStart = document.getElementById("btnStart");
            const btnShot = document.getElementById("btnShot");
            const btnSave = document.getElementById("btnSave");
            const btnReset = document.getElementById("btnReset");
            const canvas = document.getElementById("canvas");
            const photoInput = document.getElementById("photoInput");
            let stream;

            // Nyalakan kamera
            btnStart.addEventListener("click", async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "environment" },
                        audio: false
                    });
                    cam.srcObject = stream;
                    btnShot.disabled = false;
                    btnStart.disabled = true;
                } catch (err) {
                    alert("Tidak bisa mengakses kamera: " + err.message);
                }
            });

            // Jepret foto
            btnShot.addEventListener("click", () => {
                const w = cam.videoWidth;
                const h = cam.videoHeight;
                if (!w || !h) return alert("Kamera belum siap.");

                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(cam, 0, 0, w, h);

                const dataUrl = canvas.toDataURL("image/jpeg", 0.9);
                photoInput.value = dataUrl;

                btnSave.disabled = false;
                btnReset.disabled = false;
            });

            // Reset ulang foto
            btnReset.addEventListener("click", () => {
                const ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                photoInput.value = "";
                btnSave.disabled = true;
                btnReset.disabled = true;
            });

            // Matikan kamera saat keluar halaman
            window.addEventListener("beforeunload", () => {
                if (stream) stream.getTracks().forEach(track => track.stop());
            });
        });
    </script>
@endsection