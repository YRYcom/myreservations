<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Lang;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return Lang::get('filament.dashboard');
    }

    public function getHeading(): string
    {
        return Lang::get('filament.dashboard');
    }

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return Lang::get('filament.dashboard');
    }
}