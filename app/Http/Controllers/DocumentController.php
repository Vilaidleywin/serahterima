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
            'number'   => ['required', 'string', 'max:50', 'unique:documents,number'],
            'title'    => ['required', 'string', 'max:255'],
            'receiver' => ['required', 'string', 'max:100'],
            'amount'   => ['required', 'integer', 'min:0'],
            'date'     => ['required', 'date'],
            'status'   => ['required', Rule::in(['PENDING', 'DONE', 'FAILED'])],
        ]);

        // Jika input dari JS pakai format "Rp 1.000.000"
        // $data['amount'] = (int) preg_replace('/\D/', '', $request->input('amount'));

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
            'number'   => ['required', 'string', 'max:50', Rule::unique('documents', 'number')->ignore($document->id)],
            'title'    => ['required', 'string', 'max:255'],
            'receiver' => ['required', 'string', 'max:100'],
            'amount'   => ['required', 'integer', 'min:0'],
            'date'     => ['required', 'date'],
            'status'   => ['required', Rule::in(['PENDING', 'DONE', 'FAILED'])],
        ]);

        // $data['amount'] = (int) preg_replace('/\D/', '', $request->input('amount'));

        $document->update($data);

        return redirect()->route('documents.index')
            ->with('ok', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document)
    {
        $document->delete();

        return back()->with('ok', 'Dokumen berhasil dihapus.');
    }
}
