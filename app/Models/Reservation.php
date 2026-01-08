<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bien_id',
        'occupant_id',
        'number_of_guests',
        'date_start',
        'date_end',
        'comment',
        'status',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
            'status' => \App\Enums\ReservationStatus::class,
            'reminder_sent_at' => 'datetime',
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

    public function occupant()
    {
        return $this->belongsTo(Occupant::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(ReservationStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function scopeOrderedByStartDate($query)
    {
        return $query
            ->where('date_end', '>=', now()->toDateString())
            ->orderBy('date_start', 'asc')
            ->orderBy('date_end', 'asc');
    }

    public function scopeOrderedByStartDateWithoutRestrictions($query)
    {
        return $query
            ->orderBy('date_start', 'asc')
            ->orderBy('date_end', 'asc');
    }

    public function isCurrent()
    {
        return today()->between($this->date_start, $this->date_end);
    }

    public function canBeApprovedBy(User $user): bool
    {
        $managerBien = $user->biens()->where('biens.id', $this->bien_id)->first();
        
        if (!$managerBien) {
            return false;
        }
        
        return $managerBien->pivot->profile === 'gestionnaire';
    }

    public function approve(?string $comment = null, ?int $userId = null): void
    {
        $this->status = \App\Enums\ReservationStatus::Accepte;
        $this->save();
        
        $this->logStatusChange(\App\Enums\ReservationStatus::Accepte, $comment, $userId);
    }

    public function reject(?string $comment = null, ?int $userId = null): void
    {
        $this->status = \App\Enums\ReservationStatus::Refuse;
        $this->save();
        
        $this->logStatusChange(\App\Enums\ReservationStatus::Refuse, $comment, $userId);
    }

    public function resetToPending(?string $comment = null, ?int $userId = null): void
    {
        $this->status = \App\Enums\ReservationStatus::EnAttente;
        $this->save();
        
        $this->logStatusChange(\App\Enums\ReservationStatus::EnAttente, $comment, $userId);
    }

    public function logStatusChange(\App\Enums\ReservationStatus $status, ?string $comment = null, ?int $userId = null): void
    {
        ReservationStatusHistory::create([
            'reservation_id' => $this->id,
            'status' => $status,
            'user_id' => $userId,
            'comment' => $comment,
        ]);
    }
}
