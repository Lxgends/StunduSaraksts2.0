<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laiks extends Model
{
    protected $table = 'laiks';
    protected $fillable = ['sakumalaiks', 'beigulaiks'];
}
