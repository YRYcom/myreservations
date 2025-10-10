<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    //use HasFactory;

    protected $fillable = [
        'property_id',
        'email',
        'reservation_date_start',
        'reservation_date_end',
        'number_of_guests',
        'description',
    ];
}
