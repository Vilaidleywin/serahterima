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
        'description',
        'file_path',
        'status',
        'signature_path',
        'photo_path',
        'signed_at',
        'user_id',
        'signed_by',
    ];


    protected $casts = [
        'date' => 'datetime',
        'signed_at' => 'datetime',
        'photo_at' => 'datetime',
        'amount_idr' => 'float', // <- pastikan kebaca sebagai angka
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


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
