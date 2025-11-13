<?php

namespace App\Filament\Resources\Reservations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->with(['user', 'bien']);
                // Les utilisateurs normaux ne voient que leurs propres réservations
                $user = Auth::user();
                if ($user && !$user->hasRole('admin')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.resources.reservations.user.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bien.name')
                    ->label(__('filament.resources.reservations.bien.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_start')
                    ->label(__('filament.resources.reservations.date_start'))
                    ->date()
                    ->sortable(),
                TextColumn::make('date_end')
                    ->label(__('filament.resources.reservations.date_end'))
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament.resources.reservations.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament.resources.reservations.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
