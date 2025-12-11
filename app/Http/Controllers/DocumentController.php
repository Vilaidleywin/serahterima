<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    // --- DIVISION LIST ---
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
        'Resepsionis',
    ];

    public static function divisions(): array
    {
        return self::DIVISIONS;
    }

    // ============================== HELPER ==============================
    private function sanitizeAmount($val): float
    {
        $num = preg_replace('/[^0-9]/', '', (string)$val);
        return $num === '' ? 0.0 : (float)$num;
    }

    /**
     * Pecah nilai destination menjadi:
     *  - [0] => divisi tujuan (bisa dari DIVISIONS atau custom)
     *  - [1] => deskripsi tujuan (dalam kurung) kalau ada
     */
    private function splitDestination(?string $destination): array
    {
        $division = null;
        $desc = null;

        if ($destination) {
            if (preg_match('/^(.*?)\s*\((.*)\)$/', $destination, $m)) {
                $division = trim($m[1]);
                $desc     = trim($m[2]);
            } else {
                $division = trim($destination);
            }
        }

        return [$division, $desc];
    }

    // ============================== PRINTS/PHOTO ==============================
    public function printPdf(Document $document)
    {
        return Pdf::loadView('documents.print-pdf', compact('document'))
            ->setPaper('a4')
            ->stream("SerahTerima-{$document->number}.pdf");
    }

    public function printTandaTerima(Document $document)
    {
        return view('documents.print-tandaterima', ['document' => $document]);
    }

    public function photo(Document $document)
    {
        return view('documents.photo', ['title' => 'Ambil Foto', 'document' => $document]);
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
            Storage::disk('public')->put($path, $binary);
        }

        if (!$path) return back()->withErrors(['photo' => 'Silakan ambil atau unggah foto terlebih dahulu.']);

        if ($document->photo_path && Storage::disk('public')->exists($document->photo_path)) {
            Storage::disk('public')->delete($document->photo_path);
        }

        $document->update(['photo_path' => $path]);
        return redirect()->route('documents.show', $document)->with('success', 'Foto berhasil disimpan.');
    }

    // ================================= INDEX =================================
    public function index(Request $req)
    {
        $tz  = 'Asia/Jakarta';
        $now = Carbon::now($tz);

        $per = (int) $req->integer('per_page', 15);
        $per = in_array($per, [10, 15, 25, 50], true) ? $per : 15;

        $q = Document::query();

        $user = Auth::user();
        $isAdmin = $user && (method_exists($user, 'isAdmin') ? $user->isAdmin() : (($user->role ?? '') === 'admin'));

        if (!$isAdmin) {
            $userDivision = $user->division ?? null;
            if ($userDivision) {
                $q->where('division', $userDivision);
            } else {
                $q->whereNull('id');
            }
        } else {
            $userDivision = null;
        }

        if ($raw = trim((string) $req->input('search'))) {
            $s = Str::of($raw)->lower()->toString();

            $statusMap = [
                'draft' => 'DRAFT',
                'draf' => 'DRAFT',
                'submitted' => 'SUBMITTED',
                'submit' => 'SUBMITTED',
                'terkirim' => 'SUBMITTED',
                'kirim' => 'SUBMITTED',
                'rejected' => 'REJECTED',
                'reject' => 'REJECTED',
                'tolak' => 'REJECTED',
                'ditolak' => 'REJECTED',
                'disetujui' => 'SUBMITTED',
                'approved' => 'SUBMITTED',
            ];
            $guessedStatus = null;
            foreach ($statusMap as $needle => $mapped) {
                if (Str::contains($s, $needle)) {
                    $guessedStatus = $mapped;
                    break;
                }
            }

            $digits = preg_replace('/\D+/', '', $s);
            $guessedAmount = $digits !== '' ? (int)$digits : null;

            $guessedDate = null;
            $dateCandidates = [];
            if (preg_match('/\b\d{4}-\d{2}-\d{2}\b/', $s, $m)) $dateCandidates[] = $m[0];
            if (preg_match('/\b\d{1,2}\/\d{1,2}\/\d{4}\b/', $s, $m)) $dateCandidates[] = $m[0];
            if (preg_match('/\b\d{1,2}-\d{1,2}-\d{4}\b/', $s, $m)) $dateCandidates[] = $m[0];
            foreach ($dateCandidates as $cand) {
                foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $fmt) {
                    try {
                        $parsed = Carbon::createFromFormat($fmt, $cand);
                        if ($parsed) {
                            $guessedDate = $parsed->toDateString();
                            break 2;
                        }
                    } catch (\Throwable $e) {
                    }
                }
            }

            $q->where(function ($w) use ($s, $guessedStatus, $guessedAmount, $guessedDate) {
                $w->where('number', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhere('sender', 'like', "%{$s}%")
                    ->orWhere('receiver', 'like', "%{$s}%")
                    ->orWhere('division', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%");

                if ($guessedStatus) $w->orWhere('status', $guessedStatus);

                if (!is_null($guessedAmount) && $guessedAmount > 0) {
                    $w->orWhere('amount_idr', $guessedAmount)
                        ->orWhereRaw("CAST(amount_idr AS CHAR) LIKE ?", ['%' . $guessedAmount . '%']);
                }

                if ($guessedDate) $w->orWhereDate('date', $guessedDate);
            });
        }

        if ($st = $req->input('status'))  $q->where('status', $st);
        if ($dest = $req->input('destination_filter')) {
            $q->where('destination', 'like', "%{$dest}%");
        }

        $dateFrom = $req->input('date_from');
        $dateTo   = $req->input('date_to');

        if ($period = $req->input('period')) {
            $start = null;
            $end   = null;

            switch ($period) {
                case 'yesterday':
                    $start = $now->copy()->subDay()->startOfDay();
                    $end   = $now->copy()->subDay()->endOfDay();
                    break;

                case 'last_week':
                    $start = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
                    $end   = $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY);
                    break;

                case 'last_month':
                    $start = $now->copy()->subMonthNoOverflow()->startOfMonth();
                    $end   = $now->copy()->subMonthNoOverflow()->endOfMonth();
                    break;
            }

            if ($start && !$dateFrom) $dateFrom = $start->toDateString();
            if ($end && !$dateTo) $dateTo = $end->toDateString();
        }

        if ($from = $req->input('created_from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $req->input('created_to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        if ($dateFrom) {
            $q->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('date', '<=', $dateTo);
        }

        if ($overdue = $req->integer('overdue_days')) {
            $cutoff = $now->copy()->subDays($overdue)->toDateString();
            $q->where('status', 'SUBMITTED')->where(function ($w) use ($cutoff) {
                $w->where(function ($x) use ($cutoff) {
                    $x->whereNotNull('date')->whereDate('date', '<=', $cutoff);
                })
                    ->orWhere(function ($x) use ($cutoff) {
                        $x->whereNull('date')->whereDate('created_at', '<=', $cutoff);
                    });
            });
        }

        $documents = $q->reorder()
            ->latest('created_at')
            ->orderByDesc('id')
            ->paginate($per)
            ->withQueryString();

        $divisions = $q->getModel()->newQuery()
            ->when(!$isAdmin, function ($query) use ($userDivision) {
                return $query->where('division', $userDivision);
            })
            ->select('destination')->distinct()->pluck('destination')->filter()->values();

        // summary / charts data...
        $countsQuery = Document::query();
        if (!$isAdmin) {
            $countsQuery->where('division', $userDivision);
        }
        $counts = $countsQuery
            ->selectRaw('UPPER(TRIM(status)) as s, COUNT(*) as c')
            ->groupBy('s')
            ->pluck('c', 's');

        $alias = ['SUBMITTED' => 'SUBMITTED', 'APPROVED' => 'SUBMITTED', 'REJECT' => 'REJECTED', 'REJECTED' => 'REJECTED', 'DRAFT' => 'DRAFT'];
        $normalized = ['SUBMITTED' => 0, 'REJECTED' => 0, 'DRAFT' => 0];
        foreach ($counts as $k => $v) {
            $key = $alias[$k] ?? $k;
            if (isset($normalized[$key])) $normalized[$key] += (int)$v;
        }

        $submitted = (int)$normalized['SUBMITTED'];
        $rejected  = (int)$normalized['REJECTED'];
        $draft     = (int)$normalized['DRAFT'];
        $total     = $submitted + $rejected + $draft;

        $donut = ['labels' => ['SUBMITTED', 'REJECTED', 'DRAFT'], 'data' => [$submitted, $rejected, $draft]];

        $startMonth = $now->copy()->startOfMonth();
        $endMonth   = $now->copy()->endOfMonth();

        $monthCountsQuery = Document::query()
            ->whereBetween('created_at', [$startMonth, $endMonth]);
        if (!$isAdmin) {
            $monthCountsQuery->where('division', $userDivision);
        }
        $monthCounts = $monthCountsQuery
            ->selectRaw('UPPER(TRIM(status)) as s, COUNT(*) as c')
            ->groupBy('s')->pluck('c', 's');

        $mNorm = ['SUBMITTED' => 0, 'REJECTED' => 0, 'DRAFT' => 0];
        foreach ($monthCounts as $k => $v) {
            $key = $alias[$k] ?? $k;
            if (isset($mNorm[$key])) $mNorm[$key] += (int)$v;
        }
        $barLabels = ['SUBMITTED', 'REJECTED', 'DRAFT'];
        $barData   = [$mNorm['SUBMITTED'], $mNorm['REJECTED'], $mNorm['DRAFT']];

        $start = $now->copy()->subMonthsNoOverflow(11)->startOfMonth();
        $end   = $now->copy()->endOfMonth();

        $perMonthQuery = Document::query()
            ->whereBetween('created_at', [$start, $end]);
        if (!$isAdmin) {
            $perMonthQuery->where('division', $userDivision);
        }
        $perMonth = $perMonthQuery
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
            ->groupBy('ym')->pluck('c', 'ym');

        $lineLabels = [];
        $lineData   = [];
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $ym = $cursor->format('Y-m');
            $lineLabels[] = $ym;
            $lineData[]   = (int)($perMonth[$ym] ?? 0);
            $cursor->addMonth();
        }

        $createdTodayQuery = Document::whereDate('created_at', $now->toDateString());
        $createdWeekQuery  = Document::whereBetween('created_at', [$now->copy()->startOfWeek(Carbon::MONDAY), $now->copy()->endOfWeek(Carbon::SUNDAY)]);
        $createdMonthQuery = Document::whereBetween('created_at', [$startMonth, $endMonth]);

        if (!$isAdmin) {
            $createdTodayQuery->where('division', $userDivision);
            $createdWeekQuery->where('division', $userDivision);
            $createdMonthQuery->where('division', $userDivision);
        }

        $createdToday = $createdTodayQuery->count();
        $createdWeek  = $createdWeekQuery->count();
        $createdMonth = $createdMonthQuery->count();

        $overdueDays = (int) $req->integer('overdue_days', 7);
        $cutoff = $now->copy()->subDays($overdueDays)->toDateString();

        $submittedOverdueQuery = Document::where('status', 'SUBMITTED')->where(function ($w) use ($cutoff) {
            $w->where(function ($x) use ($cutoff) {
                $x->whereNotNull('date')->whereDate('date', '<=', $cutoff);
            })
                ->orWhere(function ($x) use ($cutoff) {
                    $x->whereNull('date')->whereDate('created_at', '<=', $cutoff);
                });
        });
        if (!$isAdmin) {
            $submittedOverdueQuery->where('division', $userDivision);
        }
        $submittedOverdue = $submittedOverdueQuery->count();


        return view('documents.index', [
            'title'      => 'Data Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen'],
            ],
            'documents' => $documents,
            'per_page'  => $per,
            'divisions' => $divisions,
            'total'     => $total,
            'submitted' => $submitted,
            'rejected'  => $rejected,
            'draft'     => $draft,
            'donut'     => $donut,
            'barLabels' => $barLabels,
            'barData'   => $barData,
            'lineLabels' => $lineLabels,
            'lineData'  => $lineData,
            'createdToday'       => $createdToday,
            'createdWeek'        => $createdWeek,
            'createdMonth'       => $createdMonth,
            'overdueDays'        => $overdueDays,
            'submittedOverdue'   => $submittedOverdue,
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
            'divisions' => self::DIVISIONS,
            'document' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'                  => ['required', 'string', 'max:50', 'unique:documents,number'],
            'title'                   => ['required', 'string', 'max:255'],
            'sender'                  => ['required', 'string', 'max:100'],
            'receiver'                => ['required', 'string', 'max:100'],
            'destination_desc'        => ['nullable', 'string', 'max:255'],
            'destination_desc_other'  => ['required_if:destination_desc,Other', 'nullable', 'string', 'max:255'],
            'division_tujuan'         => ['required', 'string', 'max:100'],
            'division_tujuan_other'   => ['required_if:division_tujuan,Other', 'nullable', 'string', 'max:100'],
            'amount_idr'              => ['nullable'],
            'date'                    => ['required', 'date'],
            'description'             => ['nullable', 'string'],
            'file'                    => ['required', 'file', 'max:5120'],
        ]);

        $data = Arr::except($data, ['division', 'owner_division_input']);

        // 1. Divisi Tujuan final
        $divisiTujuan = $request->input('division_tujuan');
        if (strtoupper((string)$divisiTujuan) === 'OTHER' && $request->filled('division_tujuan_other')) {
            $divisiTujuan = $request->input('division_tujuan_other');
        }
        $data['division_destination'] = $divisiTujuan;

        // 2. Deskripsi tujuan final
        $tujuanDeskripsi = $request->input('destination_desc');
        if (strtoupper((string)$tujuanDeskripsi) === 'OTHER' && $request->filled('destination_desc_other')) {
            $tujuanDeskripsi = $request->input('destination_desc_other');
        }

        // 3. Simpan ke kolom destination
        $data['destination'] = trim($divisiTujuan . ($tujuanDeskripsi ? " ({$tujuanDeskripsi})" : ""));

        unset($data['division_tujuan'], $data['division_tujuan_other'], $data['destination_desc'], $data['destination_desc_other']);

        $data['amount_idr'] = $this->sanitizeAmount($request->input('amount_idr'));
        $data['status']     = 'DRAFT';

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $data['user_id'] = Auth::id() ?? null;

        $document = Document::create($data);

        $document->division = Auth::user()->division ?? 'UNKNOWN';
        $document->save();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(Document $document)
    {
        // pecah destination menjadi [divisi_tujuan, deskripsi_tujuan]
        [$divisionTujuan, $destinationDesc] = $this->splitDestination($document->destination);

        $divisionValue = $divisionTujuan;
        $divisionOther = null;

        if ($divisionTujuan && !in_array($divisionTujuan, self::DIVISIONS, true)) {
            $divisionValue = 'Other';
            $divisionOther = $divisionTujuan;
        }

        // Siapkan daftar opsi tujuan umum (bisa kamu ganti/ambil dari DB jika mau)
        $destinationOptions = [
            'Cyber 1',
            'Gudang Cakung',
            'PID Kemayoran',
        ];

        // jika deskripsi bukan salah satu option, anggap sebagai Other dan isi destinationDescOther
        $destinationDescOther = null;
        if ($destinationDesc && !in_array($destinationDesc, $destinationOptions, true)) {
            $destinationDescOther = $destinationDesc;
            $destinationDesc = 'Other';
        }

        return view('documents.edit', [
            'title' => 'Edit Dokumen',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Data Dokumen', 'url' => route('documents.index')],
                ['label' => 'Edit Dokumen'],
            ],
            'document'        => $document,
            'divisions'       => self::DIVISIONS,
            'divisionValue'   => $divisionValue,
            'divisionOther'   => $divisionOther,
            'destinationDesc' => $destinationDesc,
            'destinationOptions' => $destinationOptions,
            'destinationDescOther' => $destinationDescOther,
        ]);
    }

    public function reject(Request $request, Document $document)
    {
        if (filled($document->signature_path)) {
            return back()->with('error', 'Dokumen sudah ditandatangani dan tidak dapat ditolak.');
        }

        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:1000'],
        ]);

        $document->status        = 'REJECTED';
        $document->reject_reason = $validated['reject_reason'];
        $document->rejected_at   = now('Asia/Jakarta');
        $document->save();

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Dokumen telah ditolak.');
    }

    public function update(Request $request, Document $document)
    {
        $wasRejected = $document->status === 'REJECTED';

        // ========== VALIDASI ==========

        $rules = [
            'number'                  => ['required', 'string', 'max:50', Rule::unique('documents', 'number')->ignore($document->id)],
            'title'                   => ['required', 'string', 'max:255'],
            'sender'                  => ['required', 'string', 'max:255'],
            'receiver'                => ['nullable', 'string', 'max:100'],

            'destination_desc'        => ['nullable', 'string', 'max:255'],
            'destination_desc_other'  => ['required_if:destination_desc,Other', 'nullable', 'string', 'max:255'],

            'division_tujuan'         => ['required', 'string', 'max:100'],
            'division_tujuan_other'   => ['required_if:division_tujuan,Other', 'nullable', 'string', 'max:100'],

            'amount_idr'              => ['nullable'],  // <-- TIDAK WAJIB
            'date'                    => ['nullable', 'date'],
            'description'             => ['nullable', 'string'],
        ];

        // FILE Wajib hanya saat belum ada
        if (empty($document->file_path)) {
            $rules['file'] = ['required', 'file', 'max:5120'];
        } else {
            $rules['file'] = ['nullable', 'file', 'max:5120'];
        }

        $data = $request->validate($rules);

        // ========== OLAH DATA ==========

        // Divisi Tujuan
        $divisiTujuan = $request->input('division_tujuan');
        if (strtoupper($divisiTujuan) === 'OTHER' && $request->filled('division_tujuan_other')) {
            $divisiTujuan = $request->division_tujuan_other;
        }

        // Keterangan Tujuan
        $tujuanDeskripsi = $request->input('destination_desc');
        if (strtoupper($tujuanDeskripsi) === 'OTHER' && $request->filled('destination_desc_other')) {
            $tujuanDeskripsi = $request->destination_desc_other;
        }

        $data['destination'] = trim($divisiTujuan . ($tujuanDeskripsi ? " ($tujuanDeskripsi)" : ""));

        unset($data['division_tujuan'], $data['division_tujuan_other'], $data['destination_desc'], $data['destination_desc_other']);

        // Nominal
        if (!empty($data['amount_idr'])) {
            $data['amount_idr'] = $this->sanitizeAmount($data['amount_idr']);
        } else {
            unset($data['amount_idr']);
        }

        // Upload file baru
        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        // Jika sebelumnya REJECTED → kembali jadi DRAFT
        if (in_array($document->status, ['SUBMITTED', 'REJECTED'])) {
            $data['status'] = 'DRAFT';
        }


        $document->update($data);

        return redirect()
            ->route('documents.index')
            ->with('success', $wasRejected
                ? 'Dokumen berhasil diperbarui dan status dikembalikan ke DRAFT.'
                : 'Dokumen berhasil diperbarui!');
    }


    public function destroy(Document $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        if ($document->photo_path && Storage::disk('public')->exists($document->photo_path)) {
            Storage::disk('public')->delete($document->photo_path);
        }
        if ($document->signature_path && Storage::disk('public')->exists($document->signature_path)) {
            Storage::disk('public')->delete($document->signature_path);
        }

        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus!');
    }

    public function show(Document $document)
    {
        $document->load('user');
        return view('documents.show', [
            'title' => 'Detail Dokumen',
            'document' => $document,
        ]);
    }

    public function sign(Document $document)
    {
        return view('documents.sign', ['title' => 'Tanda Tangan Dokumen', 'document' => $document]);
    }

    public function print(Document $document)
    {
        return view('documents.print', ['title' => 'Cetak Dokumen', 'document' => $document]);
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
        if ($document->status === 'DRAFT') $document->status = 'SUBMITTED';
        $document->signed_by = Auth::id();
        $document->save();

        return redirect()->route('documents.show', $document)->with('success', 'Tanda tangan berhasil disimpan.');
    }
}
