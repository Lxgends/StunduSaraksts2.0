<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absences extends Model
{
    use HasFactory;

    protected $table = 'absences';

    protected $fillable = [
        'absence_type',
        'pasniedzejsID',
        'kurssID',
        'sakuma_datums',
        'beigu_datums',
        'piezimes',
    ];

    protected $casts = [
        'sakuma_datums' => 'date',
        'beigu_datums' => 'date',
    ];

    public function pasniedzejs(): BelongsTo
    {
        return $this->belongsTo(Pasniedzejs::class, 'pasniedzejsID');
    }

    public function kurss(): BelongsTo
    {
        return $this->belongsTo(Kurss::class, 'kurssID');
    }

    public function ieplanotStundus()
    {
    return $this->hasMany(IeplanotStundu::class, 'absence_id');
    }
}