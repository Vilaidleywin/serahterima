<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    protected $fillable = [
        'number',
        'title',
        'receiver',
        'destination',
        'amount_idr',
        'date',
        'status',
        'description',
        'file_path'
    ];

    protected $casts = [
        'date' => 'date',
        'amount_idr' => 'float', // <- pastikan kebaca sebagai angka
    ];

    public function getAmountIdrFormattedAttribute()
{
    if ($this->amount_idr === null) {
        return '-';
    }

    return 'Rp. ' . number_format($this->amount_idr, 0, ',', '.');
}


    // Mutator: apapun inputnya (“Rp 209.000”, “200000”), disimpan jadi angka murni
    public function setAmountIdrAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['amount_idr'] = null;
            return;
        }
        $clean = preg_replace('/[^0-9]/', '', (string)$value); // hapus Rp, titik, spasi
        $this->attributes['amount_idr'] = (float)$clean;
    }
}
