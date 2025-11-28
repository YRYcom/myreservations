<?php

namespace App\Filament\Resources\Reservations\Widgets;

use Filament\Widgets\Widget;

class DisplayFinishedToggle extends Widget
{
    protected string $view = 'filament.resources.reservations.widgets.display-finished-toggle';

    public function getViewData(): array
    {
        return [
            'checked' => session('display_finished', false),
        ];
    }
}

