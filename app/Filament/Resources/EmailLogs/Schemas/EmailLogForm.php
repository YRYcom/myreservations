<?php

namespace App\Filament\Resources\EmailLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmailLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('destinataire')
                    ->required(),
                TextInput::make('sujet')
                    ->required(),
                DateTimePicker::make('sent_at')
                    ->required(),
            ]);
    }
}
