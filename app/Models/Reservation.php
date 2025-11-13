<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'bien_id',
        'date_start',
        'date_end',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
