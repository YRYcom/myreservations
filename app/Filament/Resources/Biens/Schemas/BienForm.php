<?php

namespace App\Filament\Resources\Biens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
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
                RichEditor::make('description')
                    ->label(__('filament.resources.biens.description'))
                    ->columnSpanFull(),
            ]);
    }
}
