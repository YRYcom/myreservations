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

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
