<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn () => '<link rel="stylesheet" href="' . Vite::asset('resources/css/app.css') . '">'
        );

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn () => view('filament.logout-script')
        );
    }
}
