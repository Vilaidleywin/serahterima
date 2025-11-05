<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{

    protected $fillable = [
        'number',
        'title',
        'sender',
        'receiver',
        'destination',
        'division',
        'amount_idr',
        'date',
        'status',
        'description',
        'file_path',
        'signature_path',
        'signed_at',
        'signed_by',
        'photo_path',
        'photo_at',
    ];

    protected $casts = [
         'date' => 'datetime',
    'signed_at' => 'datetime',
    'photo_at' => 'datetime',
        'amount_idr' => 'float', // <- pastikan kebaca sebagai angka
    ];

    public function signer()
    {
        return $this->belongsTo(\App\Models\User::class, 'signed_by');
    }
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
