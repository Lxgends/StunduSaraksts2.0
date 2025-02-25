<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeplanotStundu extends Model
{

    protected $table = 'ieplanot_stundu';
    protected $fillable = ['skaitlis', 'kurssID', 'laiksID', 'datumsID', 'stundaID', 'pasniedzejsID'];
    
        public function kurss()
        {
            return $this->belongsTo(Kurss::class, 'kurssID');
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

        public function datums()
        {
            return $this->belongsTo(Datums::class, 'datumsID');
        }
}
