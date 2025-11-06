<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;

class LogoutPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-left-on-rectangle';
    protected static ?string $title = 'Déconnexion';
    protected static ?string $navigationLabel = 'Déconnexion';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.pages.logout-page';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount()
    {
        Filament::auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('filament.admin.auth.login');
    }

}
