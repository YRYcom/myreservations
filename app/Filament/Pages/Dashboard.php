<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Bien;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;
use App\Filament\Resources\Reservations\ReservationResource;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $slug = 'dashboard';
    protected string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return __('filament.dashboard');
    }

    public function getHeading(): string
    {
        return __('filament.dashboard_title');

    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'biens' => collect([]),
                'user' => null,
            ];
        }
        
        $biens = $user->getAccessibleBiens();
        // Charger toutes les réservations pour tous les biens, pour tous les utilisateurs
        $biens->load(['reservations.user', 'reservations.bien']);
        
        return [
            'biens' => $biens,
            'user' => $user,
        ];
    }

    public function getReservationUrl(Bien $bien): string
    {
        $queryParams = [
            'bien_id' => $bien->id,
        ];

        $user = Auth::user();

        if ($user && ! $user->hasRole('admin')) {
            $queryParams['user_id'] = $user->id;
        }

        return ReservationResource::getUrl('create') . '?' . http_build_query($queryParams);
    }

    public function getReservationsListUrl(Bien $bien): string
    {
        // Utiliser tableSearch pour pré-remplir la recherche avec le nom du bien
        $queryParams = [
            'tableSearch' => $bien->name,
        ];

        return ReservationResource::getUrl('index') . '?' . http_build_query($queryParams);
    }

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.dashboard');
    }
}
