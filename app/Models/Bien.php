<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    //use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'adresse',
        'description',
        'image',
    ];
}
