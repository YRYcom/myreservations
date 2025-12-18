<x-filament-panels::page>
    @if($pendingReservations->count() > 0)
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #92400e;">
                {{ __('filament.dashboard.pending_reservations') }}
            </h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($pendingReservations as $reservation)
                    <div style="padding: 1rem; background: white; border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                                    {{ $reservation->bien->name }}
                                </div>
                                <div style="font-size: 0.875rem; color: #6b7280;">
                                    <div>Occupant: <span style="font-weight: 500;">{{ $reservation->occupant->name }}</span></div>
                                    <div>Dates: {{ $reservation->date_start->format('d/m/Y') }} - {{ $reservation->date_end->format('d/m/Y') }}</div>
                                    <div>Personnes: {{ $reservation->number_of_guests }}</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('edit', ['record' => $reservation->id]) }}" 
                                   style="display: inline-block; padding: 0.5rem 1rem; background-color: #3b82f6; color: white; border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                                    Gérer
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="biens-container">
        @if($biens->count() > 0)
            <div class="biens-grid">
                @foreach ($biens as $bien)
                    <div class="bien-card">
                        <div class="bien-card-content">
                            <div class="bien-card-header">
                                <div style="flex: 1;">
                                    <h3 class="bien-card-title">{{ $bien->name }}</h3>
                                </div>
                            </div>
                            @if($bien->reservations && $bien->reservations->count() > 0)
                                <div class="bien-card-reservations" style="margin: 1rem 0; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem;">
                                    <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Réservations</h4>
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($bien->reservations->take(3) as $reservation)
                                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #6b7280; {{ $reservation->isCurrent() ? 'font-weight: bold;' : '' }}">
                                                <div>
                                                    <span style="font-weight: {{ $reservation->isCurrent() ? 'bold' : '500' }};">{{ $reservation->occupant->name ?? 'N/A' }}</span>
                                                    <span style="margin: 0 0.25rem;">•</span>
                                                    <span>{{ $reservation->date_start->format('d/m/Y') }} - {{ $reservation->date_end->format('d/m/Y') }}</span>
                                                </div>
                                                @if($reservation->isCurrent())
                                                    <span style="display: inline-block; width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; margin-left: 0.5rem;"></span>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div style="font-size: 0.75rem; color: #6b7280; font-style: italic">
                                            <a href="{{ $this->getReservationsListUrl($bien) }}" style="color: #374151; text-decoration: underline;">voir toutes les réservations</a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bien-card-reservations" style="margin: 1rem 0; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem;">
                                    <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Réservations</h4>
                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                        Aucune réservation pour le moment.
                                    </div>
                                </div>
                            @endif
                            <div class="bien-card-footer">
                                <div class="bien-card-button-reserve" style="margin-top: 1rem;">
                                    <a href="{{ $this->getReservationUrl($bien) }}"
                                       class="btn-reserve"
                                       style="display: inline-block; padding: 0.5rem 1rem; background-color: #f59e0b; color: white; border-radius: 0.375rem; text-decoration: none; font-weight: 500; transition: background-color 0.2s;">
                                        Faire une réservation
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="bien-card-hover-line"></div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3>Aucun bien attribué</h3>
                <p>Vous n'avez aucun bien attribué pour le moment.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
