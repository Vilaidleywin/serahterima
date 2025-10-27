<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'number','title','receiver','amount','date','status'
    ];

    // accessor buat format rupiah
    protected $casts = ['date' => 'date'];

    public function getAmountIdrAttribute(): string
    {
        return 'Rp '.number_format($this->amount,0,',','.');
    }
}
