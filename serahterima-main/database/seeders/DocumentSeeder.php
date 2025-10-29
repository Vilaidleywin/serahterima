<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['ST-001', 'Surat Kontrak', 'Budi', 2000000, '2025-10-01', 'PENDING'],
            ['ST-002', 'Berita Acara', 'Siti', 5000000, '2025-10-05', 'DONE'],
            ['ST-003', 'Laporan Keuangan', 'Andi', 1500000, '2025-10-10', 'FAILED'],
            ['ST-004', 'Memo Internal', 'Rina', 2500000, '2025-10-12', 'PENDING'],
        ];
        foreach ($rows as $r) {
            \App\Models\Document::updateOrCreate(
                ['number' => $r[0]],
                ['title' => $r[1], 'receiver' => $r[2], 'amount' => $r[3], 'date' => $r[4], 'status' => $r[5]]
            );
        }
    }
}
