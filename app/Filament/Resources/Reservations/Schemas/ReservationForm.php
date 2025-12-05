<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use App\Models\Bien;
use App\Models\User;
use App\Models\Occupant;
use App\Models\Reservation;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->hidden()
                    ->required()
                    ->default(function () {
                        $requestedUserId = request()->input('user_id');
                        return $requestedUserId ?: Auth::id();
                    })
                    ->afterStateHydrated(function (Select $component, $state) {
                        if (empty($state)) {
                            $requestedUserId = request()->input('user_id');
                            $component->state($requestedUserId ?: Auth::id());
                        }
                    })
                    ->dehydrated(),
                Select::make('occupant_id')
                    ->label(__('filament.resources.reservations.occupant.name'))
                    ->required()
                    ->relationship('occupant', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('filament.resources.reservations.occupant.name'))
                            ->required()
                            ->unique('occupants', 'name')
                            ->validationMessages([
                                'unique' => __('filament.resources.reservations.occupant.name.unique')
                            ]),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Occupant::create([
                            'name' => $data['name'],
                        ])->getKey();
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
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->live(),
                DatePicker::make('date_end')
                    ->label(__('filament.resources.reservations.date_end'))
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->minDate(fn ($get) => $get('date_start') ?: now())
                    ->rules(['after_or_equal:date_start'])
                    ->validationMessages([
                        'after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
                    ])
                    ->live(),
                TextInput::make('number_of_guests')
                    ->label(__('filament.resources.reservations.number_of_guests'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->live()
                    ->helperText(function ($get, $record) {
                        $bienId = $get('bien_id');
                        $dateStart = $get('date_start');
                        $dateEnd = $get('date_end');
                        
                        if ($bienId) {
                            $bien = Bien::find($bienId);
                            if ($bien && $bien->capacity) {
                                if ($dateStart && $dateEnd) {
                                    $query = Reservation::where('bien_id', $bienId)
                                        ->where('date_end', '>=', $dateStart)
                                        ->where('date_start', '<=', $dateEnd);
                                    
                                    if ($record && $record->id) {
                                        $query->where('id', '!=', $record->id);
                                    }
                                    
                                    $overlappingReservations = $query->get();
                                    $alreadyBooked = $overlappingReservations->sum('number_of_guests');
                                    
                                    return __('filament.resources.reservations.capacity.max', [
                                        'capacity' => $bien->capacity,
                                        'booked' => $alreadyBooked
                                    ]);
                                }
                                
                                return __('filament.resources.reservations.capacity.select_dates', [
                                    'capacity' => $bien->capacity
                                ]);
                            }
                        }
                        return __('filament.resources.reservations.capacity.select_property');
                    }),
                Textarea::make('comment')
                    ->columnSpanFull()
                    ->label(__('filament.resources.reservations.comment')),
            ]);
    }
}
