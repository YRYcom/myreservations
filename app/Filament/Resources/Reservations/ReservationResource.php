<?php

namespace App\Filament\Resources\Reservations;

use App\Filament\Resources\Reservations\Pages\CreateReservation;
use App\Filament\Resources\Reservations\Pages\EditReservation;
use App\Filament\Resources\Reservations\Pages\ListReservations;
use App\Filament\Resources\Reservations\Schemas\ReservationForm;
use App\Filament\Resources\Reservations\Tables\ReservationsTable;
use App\Models\Reservation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReservationsTable::configure($table);
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
            'index' => ListReservations::route('/'),
            'create' => CreateReservation::route('/create'),
            'edit' => EditReservation::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'user']);
    }
    
    public static function canCreate(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'user']);
    }
    
    public static function canEdit($record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($record->user_id === $user->id) {
            return true;
        }
        
        return $record->canBeApprovedBy($user);
    }
    
    public static function canDelete($record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($record->user_id === $user->id) {
            return true;
        }
        
        return $record->canBeApprovedBy($user);
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.reservations.navigation_label');
    }

    public static function getLabel(): string
    {
        return __('filament.resources.reservations.navigation_label');
    }
}
