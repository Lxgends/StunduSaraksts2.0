<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabinets extends Model
{

    protected $table = 'kabinets';
    protected $fillable = ['vieta', 'skaitlis'];

}
