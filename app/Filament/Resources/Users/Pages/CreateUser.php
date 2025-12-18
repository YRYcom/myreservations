<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return __('filament.resources.users.create');
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        $user = $this->getRecord();
        
        if (isset($data['biens_with_profile']) && is_array($data['biens_with_profile'])) {
            $syncData = [];
            foreach ($data['biens_with_profile'] as $item) {
                if (isset($item['bien_id'])) {
                    $syncData[$item['bien_id']] = [
                        'profile' => $item['profile'] ?? 'utilisateur'
                    ];
                }
            }
            
            $user->biens()->sync($syncData);
        }
    }
}
