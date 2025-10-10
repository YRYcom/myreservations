<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    //protected static ?string $model = Reservation::class;

    protected static ?string $model = \App\Models\Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('filament.reservation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.reservation');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //Forms\Components\Select::make('property_id')
                  //  ->label('Bien')
                    //->relationship('property', 'name')
                   // ->required(),
                Forms\Components\TextInput::make('email')->email()->required()->label(__('filament.email')),
                Forms\Components\DatePicker::make('reservation_date_start')
                    ->label(__('filament.reservation_date_start'))
                    ->required(),
                Forms\Components\DatePicker::make('reservation_date_end')
                    ->label(__('filament.reservation_date_end'))
                    ->required()
                    ->after('reservation_date_start'),
                Forms\Components\TextInput::make('number_of_guests')->numeric()->required()->label(__('filament.number_of_guests')),
                Forms\Components\Textarea::make('description')->required()->label(__('filament.description')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label(__('filament.email')),
                Tables\Columns\TextColumn::make('reservation_date_start')->label(__('filament.reservation_date_start')),
                Tables\Columns\TextColumn::make('reservation_date_end')->label(__('filament.reservation_date_end')),
                Tables\Columns\TextColumn::make('number_of_guests')->label(__('filament.number_of_guests')),
                Tables\Columns\TextColumn::make('description')->label(__('filament.description')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
