<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StundaAmount extends Model
{
    protected $table = 'stunda_amount';
    protected $fillable = ['daudzums', 'stundaID', 'kurssID', 'pasniedzejsID'];


    public function kurss()
    {
        return $this->belongsTo(Kurss::class, 'kurssID');
    }

    public function pasniedzejs()
    {
        return $this->belongsTo(Pasniedzejs::class, 'pasniedzejsID');
    }

    public function stunda()
    {
        return $this->belongsTo(Stunda::class, 'stundaID');
    }
    use HasFactory;
}
