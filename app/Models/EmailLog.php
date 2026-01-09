<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'destinataire',
        'cc',
        'bcc',
        'sujet',
        'body_preview',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
