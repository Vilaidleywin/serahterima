<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $req)
    {
        $per = (int) $req->integer('per_page', 15);
        $per = in_array($per, [10,15,25,50]) ? $per : 15;

        $q = Document::query();

        if ($s = $req->input('search')) {
            $q->where(function ($w) use ($s) {
                $w->where('number','like',"%{$s}%")
                  ->orWhere('title','like',"%{$s}%")
                  ->orWhere('receiver','like',"%{$s}%")
                  ->orWhere('destination','like',"%{$s}%");
            });
        }

        if ($st = $req->input('status')) {
            $q->where('status', $st);
        }

        $documents = $q->orderByDesc('date')
                       ->orderByDesc('id')
                       ->paginate($per)
                       ->withQueryString();

        return view('documents.index', [
            'title'      => 'Data Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen'],
            ],
            'documents' => $documents,
            'per_page'  => $per,
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
            'number'      => ['required','string','max:50','unique:documents,number'],
            'title'       => ['required','string','max:255'],
            'receiver'    => ['required','string','max:100'],
            'destination' => ['nullable','string','max:255'],
            'amount_idr'  => ['required'],
            'date'        => ['required','date'],
            'status'      => ['required', Rule::in(['PENDING','DONE','FAILED'])],
            'description' => ['nullable','string'],
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
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'number'      => ['required','string','max:50', Rule::unique('documents','number')->ignore($document->id)],
            'title'       => ['required','string','max:255'],
            'receiver'    => ['nullable','string','max:100'],
            'destination' => ['nullable','string','max:255'],
            'amount_idr'  => ['nullable'],
            'date'        => ['nullable','date'],
            'status'      => ['nullable', Rule::in(['PENDING','DONE','FAILED'])],
            'description' => ['nullable','string'],
        ]);

        if (array_key_exists('amount_idr',$data) && $data['amount_idr']!=='') {
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

    public function sign(Document $document)
    {
        return view('documents.sign', [
            'title' => 'Tanda Tangan Dokumen',
            'document' => $document,
        ]);
    }

    private function sanitizeAmount($val): float
    {
        $num = preg_replace('/[^0-9]/', '', (string) $val);
        return $num === '' ? 0.0 : (float) $num;
    }
}
