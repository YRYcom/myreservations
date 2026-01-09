<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'destinataire',
        'sujet',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
