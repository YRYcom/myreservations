<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bien extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'description',
        'photo',
    ];

    protected $appends = ['photo_url'];

    /**
     * Get the URL for the bien's photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        // Pour les utilisateurs authentifiés, utiliser la route sécurisée
        if (auth()->check()) {
            return route('bien.photo', $this);
        }

        // Pour les emails et contextes non authentifiés, URL publique
        return Storage::url($this->photo);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('profile');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}