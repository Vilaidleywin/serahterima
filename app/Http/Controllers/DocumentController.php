<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentController extends Controller
{
    // Kalau nanti sudah punya login, aktifkan ini:
    // public function __construct()
    // {
    //     $this->middleware('auth')->except(['index','show']);
    // }

    public function index(Request $req)
    {
        $per = (int) $req->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50], true) ? $per : 15;

        $q = Document::query();

        // Search (number/title/receiver/destination)
        if ($s = $req->input('search')) {
            $q->where(function ($w) use ($s) {
                $w->where('number', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhere('receiver', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%");
            });
        }

        // Status (SUBMITTED/REJECTED)
        if ($st = $req->input('status')) {
            $q->where('status', $st);
        }

        // Division
        if ($div = $req->input('division')) {
            $q->where('division', $div);
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

    private array $divisions = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];

    public function create()
    {
        return view('documents.create', [
            'title' => 'Tambah Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen', 'url' => route('documents.index')],
                ['label' => 'Tambah Dokumen'],
            ],
            'divisions' => $this->divisions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'      => ['required', 'string', 'max:50', 'unique:documents,number'],
            'title'       => ['required', 'string', 'max:255'],
            'sender'      => ['required', 'string', 'max:100'],
            'receiver'    => ['required', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:255'],
            'division'    => ['nullable', 'string', 'max:100'],
            'amount_idr'  => ['required'],
            'date'        => ['required', 'date'],
            'status'      => ['required', Rule::in(['SUBMITTED', 'REJECTED'])],
            'description' => ['nullable', 'string'],
        ]);

        $data['amount_idr'] = $this->sanitizeAmount($data['amount_idr']);

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
            'document' => $document,
            'divisions' => $this->divisions,
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'number'      => ['required', 'string', 'max:50', Rule::unique('documents', 'number')->ignore($document->id)],
            'title'       => ['required', 'string', 'max:255'],
            'sender'      => ['required', 'string', 'max:255'],
            'receiver'    => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:255'],
            'division'    => 'nullable|string|max:100',
            'amount_idr'  => ['nullable'],
            'date'        => ['nullable', 'date'],
            'status'      => ['nullable', Rule::in(['SUBMITTED', 'REJECTED'])],
            'description' => ['nullable', 'string'],
        ]);

        if (array_key_exists('amount_idr', $data) && $data['amount_idr'] !== '') {
            $data['amount_idr'] = $this->sanitizeAmount($data['amount_idr']);
        } else {
            unset($data['amount_idr']);
        }

        $document->update($data);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diperbarui!');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus!');
    }

    public function show(Document $document)
    {
        return view('documents.show', [
            'title' => 'Detail Dokumen',
            'document' => $document,
        ]);
    }

    // ====== TANDA TANGAN ======

    public function sign(Document $document)
    {
        return view('documents.sign', [
            'title' => 'Tanda Tangan Dokumen',
            'document' => $document,
        ]);
    }

    public function signStore(Request $request, Document $document)
    {
        $dataUrl = $request->input('signature'); // data:image/png;base64,AAAA...
        if (!$dataUrl || !str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return back()->withErrors(['signature' => 'Tanda tangan tidak valid.'])->withInput();
        }

        // decode base64
        $png = base64_decode(Str::after($dataUrl, 'data:image/png;base64,'));
        if ($png === false || strlen($png) < 100) {
            return back()->withErrors(['signature' => 'Gagal memproses tanda tangan.'])->withInput();
        }

        // simpan file
        $dir = 'signatures';
        $name = 'sign-' . $document->id . '-' . time() . '.png';
        Storage::disk('public')->put("$dir/$name", $png);

        // hapus file lama (opsional)
        if ($document->signature_path && Storage::disk('public')->exists($document->signature_path)) {
            Storage::disk('public')->delete($document->signature_path);
        }

        // kalau belum ada login, Auth::id() bakal null (aman karena kolom nullable)
        $userId = Auth::id();

        $document->update([
            'signature_path' => "$dir/$name",
            'signed_at'      => Carbon::now(),
            'signed_by'      => $userId, // nullable
        ]);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Tanda tangan berhasil disimpan.');
    }

    // ====== UTIL ======
    private function sanitizeAmount($val): float
    {
        $num = preg_replace('/[^0-9]/', '', (string) $val);
        return $num === '' ? 0.0 : (float) $num;
    }
}
