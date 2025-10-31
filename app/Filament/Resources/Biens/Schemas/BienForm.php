<?php

namespace App\Filament\Resources\Biens\Schemas;

use Filament\Forms\Components\TextInput;
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
            ]);
    }
}
