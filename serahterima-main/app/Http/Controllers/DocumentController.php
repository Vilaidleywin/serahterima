<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $req)
    {
        $q = Document::query();

        if ($s = $req->input('search')) {
            $q->where(fn($w) => $w
                ->where('number', 'like', "%{$s}%")
                ->orWhere('title', 'like', "%{$s}%")
                ->orWhere('receiver', 'like', "%{$s}%"));
        }

        if ($st = $req->input('status')) {
            $q->where('status', $st);
        }

        $documents = $q->orderByDesc('date')->paginate(10)->withQueryString();

        return view('documents.index', [
            'title' => 'Data Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen'],
            ],
            'documents' => $documents,
        ]);
    }

    public function create()
    {
        return view('documents.create', [
            'title' => 'Tambah Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen', 'url' => route('documents.index')],
                ['label' => 'Tambah Dokumen'],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'      => ['required', 'string', 'max:50', 'unique:documents,number'],
            'title'       => ['required', 'string', 'max:255'],
            'receiver'    => ['required', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:255'],
            'amount_idr'  => ['required'],
            'date'        => ['required', 'date'],
            'status'      => ['required', Rule::in(['PENDING', 'DONE', 'FAILED'])],
            'description' => ['nullable', 'string'],
        ]);

        // Bersihkan "Rp" atau titik
        if (isset($data['amount_idr'])) {
            $data['amount_idr'] = (float) preg_replace('/[^0-9]/', '', $data['amount_idr']);
        }

        Document::create($data);

        return redirect()->route('documents.index')
            ->with('ok', 'Dokumen berhasil ditambahkan.');
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
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'number'      => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'receiver'    => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'amount_idr'  => 'nullable',                  // jangan numeric dulu, kita bersihkan manual
            'date'        => 'nullable|date',
            'status'      => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (array_key_exists('amount_idr', $data) && $data['amount_idr'] !== null) {
            $data['amount_idr'] = (float) preg_replace('/[^0-9]/', '', (string) $data['amount_idr']);
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

    public function sign(Document $document)
    {
        return view('documents.sign', [
            'title' => 'Tanda Tangan Dokumen',
            'document' => $document,
        ]);
    }
}
