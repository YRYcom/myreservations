<?php

namespace App\Filament\Resources\Biens\Pages;

use App\Filament\Resources\Biens\BienResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBien extends CreateRecord
{
    protected static string $resource = BienResource::class;

    public function getTitle(): string
    {
        return __('filament.resources.biens.create');
    }

    protected function getFormActions(): array
    {
        $actions = [
            $this->getCreateFormAction(),
        ];

        if (auth()->user()?->hasRole('admin')) {
            $actions[] = $this->getCreateAnotherFormAction();
        }

        $actions[] = $this->getCancelFormAction();

        return $actions;
    }
}
