<?php

namespace App\Filament\Resources\BienResource\Pages;

use App\Filament\Resources\BienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\App;

class ListBien extends ListRecords
{
    protected static string $resource = BienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('filament.add_property')),
        ];
    }
}
