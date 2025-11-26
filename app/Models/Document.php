<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'title',
        'sender',
        'receiver',
        'destination',
        'amount_idr',
        'date',
        'description',
        'file_path',
        'status',
        'signature_path',
        'photo_path',
        'signed_at',
        'reject_reason',
        'division_destination',
        'user_id',
        'signed_by',
    ];

    protected $casts = [
        'date' => 'datetime',
        'signed_at' => 'datetime',
        'amount_idr' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function setAmountIdrAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['amount_idr'] = null;
            return;
        }
        $clean = preg_replace('/[^0-9]/', '', (string)$value);
        $this->attributes['amount_idr'] = (float)$clean;
    }

    protected static function booted()
    {
        // Prevent changing division via normal update
        static::updating(function ($doc) {
            if ($doc->isDirty('division')) {
                $doc->division = $doc->getOriginal('division');
            }
        });

        // If creating in a web request, fill division from auth user if empty
        static::creating(function ($doc) {
            if (empty($doc->division) && auth()->check()) {
                $doc->division = auth()->user()->division ?? 'UNKNOWN';
            }
        });
    }
}
