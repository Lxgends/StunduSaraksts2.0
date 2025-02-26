<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeplanotStundu extends Model
{
    use HasFactory;

    protected $table = 'ieplanot_stundu';
    protected $fillable = ['skaitlis', 'kurssID', 'laiksID', 'datumsID', 'kabinetaID', 'stundaID', 'pasniedzejsID'];

    public function kurss()
    {
        return $this->belongsTo(Kurss::class, 'kurssID');
    }

    public function pasniedzejs()
    {
        return $this->belongsTo(Pasniedzejs::class, 'pasniedzejsID');
    }

    public function kabinets()
    {
        return $this->belongsTo(Kabinets::class, 'kabinetaID');
    }

    public function laiks()
    {
        return $this->belongsTo(Laiks::class, 'laiksID');
    }

    public function stunda()
    {
        return $this->belongsTo(Stunda::class, 'stundaID');
    }

    public function datums()
    {
        return $this->belongsTo(Datums::class, 'datumsID');
    }
}
