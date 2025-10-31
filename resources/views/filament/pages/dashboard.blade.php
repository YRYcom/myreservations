<x-filament-panels::page>
    @php
        $biens = $this->getBiens();
        $user = Auth::user();
    @endphp

    <div class="biens-container">
        @if($user->hasRole('admin'))
            <div class="biens-header">
                <h1>Tous les Biens</h1>
                <p>{{ $biens->count() }} bien{{ $biens->count() > 1 ? 's' : '' }}</p>
            </div>
        @else   
            <div class="biens-header">
                <h1>Mes Biens</h1>
                <p>{{ $biens->count() }} bien{{ $biens->count() > 1 ? 's' : '' }} attribué{{ $biens->count() > 1 ? 's' : '' }}</p>
            </div>
        @endif

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
                            @if(isset($bien->created_at))
                                <div class="bien-card-footer">
                                    <p>Ajouté le {{ $bien->created_at->format('d/m/Y') }}</p>
                                </div>
                            @endif
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
