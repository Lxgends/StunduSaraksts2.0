<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datums extends Model
{

    protected $table = 'datums';
    protected $fillable = ['PirmaisDatums', 'PedejaisDatums'];

}
