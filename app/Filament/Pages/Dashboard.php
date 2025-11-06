<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Bien;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $slug = 'dashboard';
    protected string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Accueil';

    public function getBiens()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return Bien::all();
        }

        return $user->biens;
    }

    public function getTitle(): string
    {
        return __('filament.dashboard');
    }

    public function getHeading(): string
    {
        return __('filament.dashboard');
    }

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.dashboard');
    }
}
