<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case EnAttente = 'en_attente';
    case Accepte = 'accepte';
    case Refuse = 'refuse';

    public function label(): string
    {
        return match($this) {
            self::EnAttente => __('filament.enums.reservation_status.en_attente'),
            self::Accepte => __('filament.enums.reservation_status.accepte'),
            self::Refuse => __('filament.enums.reservation_status.refuse'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::EnAttente => 'warning',
            self::Accepte => 'success',
            self::Refuse => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
