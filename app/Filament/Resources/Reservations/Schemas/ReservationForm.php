<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Bien;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('filament.resources.reservations.user.name'))
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => Auth::id())
                    ->disabled(fn () => !Auth::user()?->hasRole('admin'))
                    ->dehydrated(),
                Select::make('bien_id')
                    ->label(__('filament.resources.reservations.bien.name'))
                    ->required()
                    ->relationship(
                        'bien',
                        'name',
                        function ($query) {
                            $user = Auth::user();
                            if (!$user || $user->hasRole('admin')) {
                                return $query;
                            }
                            // Pour les utilisateurs normaux, filtrer par leurs biens accessibles
                            $bienIds = $user->getAccessibleBiens()->pluck('id')->toArray();
                            return $query->whereIn('id', $bienIds);
                        }
                    )
                    ->searchable()
                    ->preload(),
                DatePicker::make('date_start')
                    ->label(__('filament.resources.reservations.date_start'))
                    ->required()
                    ->native(false)
                    ->live(),
                DatePicker::make('date_end')
                    ->label(__('filament.resources.reservations.date_end'))
                    ->required()
                    ->native(false)
                    ->minDate(fn ($get) => $get('date_start') ?: now())
                    ->rules(['after_or_equal:date_start'])
                    ->validationMessages([
                        'after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
                    ]),
                Textarea::make('comment')
                    ->columnSpanFull(),
            ]);
    }
}
