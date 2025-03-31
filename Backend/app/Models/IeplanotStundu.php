<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IeplanotStundu extends Model
{
    use HasFactory;
    
    protected $table = 'ieplanot_stundu';
    
    protected $fillable = [
        'kurssID',
        'datumsID',
        'skaitlis',
        'laiksID',
        'stundaID',
        'pasniedzejsID',
        'kabinetaID',
    ];

    public function kurss()
    {
        return $this->belongsTo(Kurss::class, 'kurssID');
    }
    
    public function datums()
    {
        return $this->belongsTo(Datums::class, 'datumsID');
    }
    
    public function laiks()
    {
        return $this->belongsTo(Laiks::class, 'laiksID');
    }
    
    public function stunda()
    {
        return $this->belongsTo(Stunda::class, 'stundaID');
    }
    
    public function pasniedzejs()
    {
        return $this->belongsTo(Pasniedzejs::class, 'pasniedzejsID');
    }
    
    public function kabinets()
    {
        return $this->belongsTo(Kabinets::class, 'kabinetaID');
    }
}