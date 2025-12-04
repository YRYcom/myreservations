<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'bien_id',
        'occupant_id',
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

    public function occupant()
    {
        return $this->belongsTo(Occupant::class);
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
        return now()->between($this->date_start, $this->date_end);
    }
}
