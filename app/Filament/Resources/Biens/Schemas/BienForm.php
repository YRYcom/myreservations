<?php

namespace App\Filament\Resources\Biens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.resources.biens.name'))
                    ->required(),
                TextInput::make('capacity')
                    ->label(__('filament.resources.biens.capacity.helper'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Textarea::make('arrivals_consignes')
                    ->label(__('filament.resources.biens.arrivals_consignes'))
                    ->required(),
                Textarea::make('departures_consignes')
                    ->label(__('filament.resources.biens.departures_consignes'))
                    ->required(),
            ]);
    }
}
