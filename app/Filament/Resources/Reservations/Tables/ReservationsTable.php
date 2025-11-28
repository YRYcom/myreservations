<?php

namespace App\Filament\Resources\Reservations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['user', 'bien', 'occupant']);
                $user = Auth::user();
                if ($user && !$user->hasRole('admin')) {
                    $bienIds = $user->getAccessibleBiens()->pluck('id')->toArray();
                    $query->whereIn('bien_id', $bienIds);
                }
                if (! session('display_finished', false)) {
                    $today = now()->startOfDay();
                    $query->where(function (Builder $query) use ($today) {
                        $query->whereNull('date_end')
                            ->orWhereDate('date_end', '>=', $today);
                    });
                }
            })
            ->columns([
                TextColumn::make('occupant.name')
                    ->label(__('filament.resources.reservations.occupant'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bien.name')
                    ->label(__('filament.resources.reservations.bien.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_start')
                    ->label(__('filament.resources.reservations.date_start'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('date_end')
                    ->label(__('filament.resources.reservations.date_end'))
                    ->date('d/m/Y')
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
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => \App\Filament\Resources\Reservations\ReservationResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
