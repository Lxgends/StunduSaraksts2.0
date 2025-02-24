<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasniedzejs extends Model
{
    protected $table = 'pasniedzejs';
    protected $fillable = ['Vards', 'Uzvards', 'KabinetsID'];


    public function kabinets()
{
    return $this->belongsTo(Kabinets::class, 'KabinetsID');
}
}
