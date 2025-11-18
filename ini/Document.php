<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    /**
     * NOTE:
     * - 'division' intentionally removed from $fillable to prevent mass-assignment.
     * - Server must set $document->division = Auth::user()->division when creating.
     */
    protected $fillable = [
        'number',
        'title',
        'sender',
        'receiver',
        'destination',
        // 'division',
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

    /**
     * Optional extra safety: explicitly guard division as well.
     * If you prefer guarded approach, comment out $fillable and use $guarded instead.
     */
    // protected $guarded = ['id', 'division'];

    protected $casts = [
        'date'      => 'datetime',
        'signed_at' => 'datetime',
        // 'photo_at' cast removed (not present as attribute in this model)
        'amount_idr' => 'float',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function signer()
    {
        return $this->belongsTo(\App\Models\User::class, 'signed_by');
    }

    // Accessor: formatted amount
    public function getAmountIdrFormattedAttribute()
    {
        if ($this->amount_idr === null) {
            return '-';
        }

        return 'Rp. ' . number_format($this->amount_idr, 0, ',', '.');
    }

    // Mutator: bersihkan input amount apapun bentuknya jadi angka murni
    public function setAmountIdrAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['amount_idr'] = null;
            return;
        }
        $clean = preg_replace('/[^0-9]/', '', (string)$value);
        $this->attributes['amount_idr'] = $clean === '' ? null : (float)$clean;
    }
}
