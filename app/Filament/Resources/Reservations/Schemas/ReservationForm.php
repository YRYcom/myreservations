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
use Carbon\Carbon;

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
                        $numberOfGuests = (int) $get('number_of_guests');
                        
                        if (!$bienId) {
                            return __('filament.resources.reservations.capacity.select_property');
                        }
                        
                        $bien = Bien::find($bienId);
                        if (!$bien || !$bien->capacity) {
                            return null;
                        }
                        
                        if (!$dateStart || !$dateEnd) {
                            return __('filament.resources.reservations.capacity.select_dates', [
                                'capacity' => $bien->capacity
                            ]);
                        }
                        
                        $query = Reservation::where('bien_id', $bienId)
                            ->where('date_end', '>=', $dateStart)
                            ->where('date_start', '<=', $dateEnd);
                        
                        if ($record && $record->id) {
                            $query->where('id', '!=', $record->id);
                        }
                        
                        $overlappingReservations = $query->get();
                        $alreadyBooked = $overlappingReservations->sum('number_of_guests');
                        
                        if ($numberOfGuests > 0) {
                            $start = Carbon::parse($dateStart);
                            $end = Carbon::parse($dateEnd);
                            $conflictingDates = [];
                            
                            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                                $dateString = $date->format('Y-m-d');
                                
                                $queryDate = Reservation::where('bien_id', $bienId)
                                    ->whereDate('date_start', '<=', $dateString)
                                    ->whereDate('date_end', '>=', $dateString);
                                
                                if ($record && $record->id) {
                                    $queryDate->where('id', '!=', $record->id);
                                }
                                
                                $reservedOnDate = $queryDate->sum('number_of_guests');
                                
                                if ($reservedOnDate + $numberOfGuests > $bien->capacity) {
                                    $conflictingDates[] = $date->format('d/m/Y') . ' (' . ($reservedOnDate + $numberOfGuests) . '/' . $bien->capacity . ')';
                                }
                            }
                            
                            if (!empty($conflictingDates)) {
                                $datesList = implode(', ', $conflictingDates);
                                return new \Illuminate\Support\HtmlString(
                                    '<div style="color: #dc2626; font-weight: 600; margin-top: 0.5rem;">' .
                                    __('filament.resources.reservations.capacity.exceeded', ['capacity' => $bien->capacity]) .
                                    '</div>' .
                                    '<div style="color: #dc2626; margin-top: 0.25rem;">' . $datesList . '</div>'
                                );
                            }
                        }
                        
                        return __('filament.resources.reservations.capacity.max', [
                            'capacity' => $bien->capacity,
                            'booked' => $alreadyBooked
                        ]);
                    }),
                Textarea::make('comment')
                    ->columnSpanFull()
                    ->label(__('filament.resources.reservations.comment')),
            ]);
    }
}
