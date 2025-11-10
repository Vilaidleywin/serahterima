<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    // --- TAMBAHAN: jadikan constant + getter statis ---
    public const DIVISIONS = [
        'Pengadaan',
        'Pembelian',
        'Pergudangan',
        'Pengawas Internal',
        'Pelayanan dan Jasa',
        'Pemeliharaan',
        'IT Komersial',
        'Pemasaran',
        'Pembekalan',
        'Komersial Asset',
        'SDM & Umum',
        'IT Internal',
        'Perpajakan',
        'Akutansi',
        'Menajemen Resiko',
        'Manager Treasury',
        'Security',
        'Other'
    ];

    public static function divisions(): array
    {
        return self::DIVISIONS;
    }
    // ---------------------------------------------------

    // public function __construct() { ... }

    public function printPdf(Document $document)
    {
        return Pdf::loadView('documents.print-pdf', compact('document'))
            ->setPaper('a4')
            ->stream("SerahTerima-{$document->number}.pdf");
    }

    public function printTandaTerima(Document $document)
    {
        return view('documents.print-tandaterima', [
            'document' => $document,
        ]);
    }

    public function photo(Document $document)
    {
        return view('documents.photo', [
            'title'    => 'Ambil Foto',
            'document' => $document,
        ]);
    }

    public function photoStore(Request $request, Document $document)
    {
        if ($document->status === 'REJECTED') {
            return back()->with('error', 'Dokumen ditolak dan tidak dapat diunggah foto.');
        }

        $request->validate([
            'photo'      => ['nullable', 'string'],
            'photo_file' => ['nullable', 'image', 'max:5120'],
        ]);

        $path = null;

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('document-photos', 'public');
        } elseif ($dataUrl = $request->input('photo')) {
            if (!preg_match('#^data:image/(png|jpe?g);base64,#i', $dataUrl)) {
                return back()->withErrors(['photo' => 'Foto tidak valid.'])->withInput();
            }
            $binary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $dataUrl), true);
            if ($binary === false || strlen($binary) < 1000) {
                return back()->withErrors(['photo' => 'Gagal memproses foto.'])->withInput();
            }
            $filename = 'doc-' . $document->id . '-' . time() . '.jpg';
            $path = 'document-photos/' . $filename;
            \Storage::disk('public')->put($path, $binary);
        }

        if (!$path) {
            return back()->withErrors(['photo' => 'Silakan ambil atau unggah foto terlebih dahulu.']);
        }

        if ($document->photo_path && \Storage::disk('public')->exists($document->photo_path)) {
            \Storage::disk('public')->delete($document->photo_path);
        }

        $document->update(['photo_path' => $path]);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Foto berhasil disimpan.');
    }

    public function index(Request $req)
    {
        $per = (int) $req->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50], true) ? $per : 15;

        $q = Document::query();

        if ($s = $req->input('search')) {
            $q->where(function ($w) use ($s) {
                $w->where('number', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhere('receiver', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%");
            });
        }

        if ($st = $req->input('status')) {
            $q->where('status', $st);
        }

        if ($div = $req->input('division')) {
            $q->where('division', $div);
        }

        if ($period = $req->input('period')) {
            $now = Carbon::now('Asia/Jakarta');
            $now->locale('id_ID');
            $startWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
            $endWeek   = $now->copy()->endOfWeek(Carbon::SUNDAY);

            if ($period === 'today') {
                $q->whereDate('created_at', $now->toDateString());
            } elseif ($period === 'week') {
                $q->whereBetween('created_at', [$startWeek, $endWeek]);
            } elseif ($period === 'month') {
                $q->whereBetween('created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth(),
                ]);
            }
        }

        if ($from = $req->input('created_from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $req->input('created_to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        if ($df = $req->input('date_from')) {
            $q->whereDate('date', '>=', $df);
        }
        if ($dt = $req->input('date_to')) {
            $q->whereDate('date', '<=', $dt);
        }

        if ($overdue = $req->integer('overdue_days')) {
            $cutoff = Carbon::now('Asia/Jakarta')->subDays($overdue)->toDateString();
            $q->where('status', 'SUBMITTED')
                ->where(function ($w) use ($cutoff) {
                    $w->where(function ($x) use ($cutoff) {
                        $x->whereNotNull('date')->whereDate('date', '<=', $cutoff);
                    })->orWhere(function ($x) use ($cutoff) {
                        $x->whereNull('date')->whereDate('created_at', '<=', $cutoff);
                    });
                });
        }

        $documents = $q->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($per)
            ->withQueryString();

        $divisions = Document::query()
            ->select('division')
            ->distinct()
            ->pluck('division')
            ->filter()
            ->values();

        return view('documents.index', [
            'title'      => 'Data Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen'],
            ],
            'documents' => $documents,
            'per_page'  => $per,
            'divisions' => $divisions,
        ]);
    }

    // private array $divisions = [...];  // ⬅️ DIHAPUS, diganti constant

    public function create()
    {
        return view('documents.create', [
            'title' => 'Tambah Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen', 'url' => route('documents.index')],
                ['label' => 'Tambah Dokumen'],
            ],
            'divisions' => self::DIVISIONS, // ⬅️ ganti dari $this->divisions
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'         => ['required', 'string', 'max:50', 'unique:documents,number'],
            'title'          => ['required', 'string', 'max:255'],
            'sender'         => ['required', 'string', 'max:100'],
            'receiver'       => ['required', 'string', 'max:100'],
            'destination'    => ['nullable', 'string', 'max:255'],
            'division'       => ['nullable', 'string', 'max:100'],
            'division_other' => ['nullable', 'string', 'max:100'],
            'amount_idr'     => ['required'],
            'date'           => ['required', 'date'],
            'description'    => ['nullable', 'string'],
            'file'           => ['nullable', 'file', 'max:5120'],
        ]);

        if (strtoupper((string)$data['division']) === 'OTHER' && $request->filled('division_other')) {
            $data['division'] = $request->input('division_other');
        }

        $data['amount_idr'] = $this->sanitizeAmount($data['amount_idr']);
        $data['status']     = 'DRAFT';

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('documents', 'public');
            $data['file_path'] = $path;
        }

        Document::create($data);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(Document $document)
    {
        return view('documents.edit', [
            'title' => 'Edit Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen', 'url' => route('documents.index')],
                ['label' => 'Edit Dokumen'],
            ],
            'document'  => $document,
            'divisions' => self::DIVISIONS, // ⬅️ ganti dari $this->divisions
        ]);
    }

    public function reject(Request $request, Document $document)
    {
        if ($document->status !== 'REJECTED') {
            $document->status = 'REJECTED';
            $document->save();
        }

        return back()->with('success', 'Dokumen telah ditolak.');
    }

    public function update(Request $request, Document $document)
    {
        if ($document->status === 'REJECTED') {
            return back()->with('error', 'Dokumen ditolak dan tidak dapat diedit.');
        }

        $data = $request->validate([
            'number'      => ['required', 'string', 'max:50', Rule::unique('documents', 'number')->ignore($document->id)],
            'title'       => ['required', 'string', 'max:255'],
            'sender'      => ['required', 'string', 'max:255'],
            'receiver'    => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:255'],
            'division'    => 'nullable|string|max:100',
            'amount_idr'  => ['nullable'],
            'date'        => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'file'        => ['nullable', 'file', 'max:5120'],
        ]);

        if (array_key_exists('amount_idr', $data) && $data['amount_idr'] !== '') {
            $data['amount_idr'] = $this->sanitizeAmount($data['amount_idr']);
        } else {
            unset($data['amount_idr']);
        }

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $document->update($data);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diperbarui!');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihaaapus!');
    }

    public function show(Document $document)
    {
        return view('documents.show', [
            'title'    => 'Detail Dokumen',
            'document' => $document,
        ]);
    }

    public function sign(Document $document)
    {
        return view('documents.sign', [
            'title'    => 'Tanda Tangan Dokumen',
            'document' => $document,
        ]);
    }

    public function print(Document $document)
    {
        return view('documents.print', [
            'title'    => 'Cetak Dokumen',
            'document' => $document,
        ]);
    }

    public function signStore(Request $request, Document $document)
    {
        if ($document->status === 'REJECTED') {
            return back()->with('error', 'Dokumen ditolak dan tidak dapat ditandatangani.');
        }

        $dataUrl = $request->input('signature');
        if (!$dataUrl || !str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return back()->withErrors(['signature' => 'Tanda tangan tidak valid.'])->withInput();
        }

        $png = base64_decode(Str::after($dataUrl, 'data:image/png;base64,'));
        if ($png === false || strlen($png) < 100) {
            return back()->withErrors(['signature' => 'Gagal memproses tanda tangan.'])->withInput();
        }

        $dir  = 'signatures';
        $name = 'sign-' . $document->id . '-' . time() . '.png';
        Storage::disk('public')->put("$dir/$name", $png);

        if ($document->signature_path && Storage::disk('public')->exists($document->signature_path)) {
            Storage::disk('public')->delete($document->signature_path);
        }

        $document->signature_path = "$dir/$name";
        $document->signed_at      = now('Asia/Jakarta');
        if ($document->status === 'DRAFT') {
            $document->status = 'SUBMITTED';
        }
        $document->signed_by = Auth::id();
        $document->save();

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Tanda tangan berhasil disimpan.');
    }

    private function sanitizeAmount($val): float
    {
        $num = preg_replace('/[^0-9]/', '', (string) $val);
        return $num === '' ? 0.0 : (float) $num;
    }
}
