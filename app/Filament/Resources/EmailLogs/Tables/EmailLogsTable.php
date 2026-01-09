<?php

namespace App\Filament\Resources\EmailLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('destinataire')
                    ->label('Destinataire')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('sujet')
                    ->label('Sujet')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('sent_at')
                    ->label('Envoyé le')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->default(true)
                    ->sortable(['direction' => 'desc']),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }
}
