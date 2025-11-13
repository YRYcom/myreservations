<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Bien;
use App\Models\User;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('filament.resources.reservations.user.name'))
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default(function () {
                        $requestedUserId = request()->input('user_id');
                        if ($requestedUserId) {
                            return $requestedUserId;
                        }

                        $currentUser = Auth::user();

                        return $currentUser && ! $currentUser->hasRole('admin')
                            ? $currentUser->id
                            : null;
                    })
                    ->disabled(fn () => !Auth::user()?->hasRole('admin'))
                    ->dehydrated()
                    ->live()
                    ->afterStateHydrated(function (Select $component, $state) {
                        if (filled($state)) {
                            return;
                        }

                        $requestedUserId = request()->input('user_id');
                        if ($requestedUserId) {
                            $component->state($requestedUserId);
                            return;
                        }

                        $currentUser = Auth::user();
                        if ($currentUser && ! $currentUser->hasRole('admin')) {
                            $component->state($currentUser->id);
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (! Auth::user()?->hasRole('admin')) {
                            return;
                        }

                        $selectedBienId = $get('bien_id');

                        if (! $selectedBienId) {
                            return;
                        }

                        $selectedUser = $state ? User::find($state) : null;

                        if (! $selectedUser) {
                            $set('bien_id', null);
                            return;
                        }

                        $allowedBienIds = $selectedUser->getAccessibleBiens()->pluck('id')->toArray();

                        if (! in_array($selectedBienId, $allowedBienIds, true)) {
                            $set('bien_id', null);
                        }
                    }),
                Select::make('bien_id')
                    ->label(__('filament.resources.reservations.bien.name'))
                    ->required()
                    ->options(function ($get) {
                        $currentUser = Auth::user();
                        $selectedUserId = $get('user_id');
                        
                        if ($selectedUserId) {
                            $selectedUser = User::find($selectedUserId);
                            if ($selectedUser) {
                                return $selectedUser->getAccessibleBiens()->pluck('name', 'id')->toArray();
                            }
                        }
                        if ($currentUser) {
                            return $currentUser->getAccessibleBiens()->pluck('name', 'id')->toArray();
                        }
                        
                        return [];
                    })
                    ->getSearchResultsUsing(function (string $search, $get) {
                        $currentUser = Auth::user();
                        $selectedUserId = $get('user_id');
                        
                        $query = Bien::query()->where('name', 'like', "%{$search}%");
                        
                        if ($selectedUserId) {
                            $selectedUser = User::find($selectedUserId);
                            if ($selectedUser) {
                                $bienIds = $selectedUser->getAccessibleBiens()->pluck('id')->toArray();
                                $query->whereIn('id', $bienIds);
                            }
                        } elseif ($currentUser && !$currentUser->hasRole('admin')) {
                            $bienIds = $currentUser->getAccessibleBiens()->pluck('id')->toArray();
                            $query->whereIn('id', $bienIds);
                        }
                        
                        return $query->limit(50)->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->default(fn () => request()->input('bien_id'))
                    ->reactive(),
                DatePicker::make('date_start')
                    ->label(__('filament.resources.reservations.date_start'))
                    ->required()
                    ->native(false)
                    ->live(),
                DatePicker::make('date_end')
                    ->label(__('filament.resources.reservations.date_end'))
                    ->required()
                    ->native(false)
                    ->minDate(fn ($get) => $get('date_start') ?: now())
                    ->rules(['after_or_equal:date_start'])
                    ->validationMessages([
                        'after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
                    ]),
                Textarea::make('comment')
                    ->columnSpanFull(),
            ]);
    }
}
