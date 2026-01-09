<?php

namespace App\Filament\Resources\EmailLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmailLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('destinataire')
                    ->label('Destinataire')
                    ->copyable(),
                TextEntry::make('sujet')
                    ->label('Sujet'),
                TextEntry::make('sent_at')
                    ->label('Envoyé le')
                    ->dateTime('d/m/Y H:i:s'),
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i:s')
                    ->placeholder('-'),
            ]);
    }
}
