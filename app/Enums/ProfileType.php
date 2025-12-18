<?php

namespace App\Enums;

enum ProfileType: string
{
    case Utilisateur = 'utilisateur';
    case Gestionnaire = 'gestionnaire';

    public function label(): string
    {
        return match($this) {
            self::Utilisateur => __('filament.enums.profile_type.utilisateur'),
            self::Gestionnaire => __('filament.enums.profile_type.gestionnaire'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
