<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;
use App\Models\User;

class ReservationStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'reservation_status_history';

    protected $fillable = [
        'reservation_id',
        'status',
        'user_id',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\ReservationStatus::class,
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
