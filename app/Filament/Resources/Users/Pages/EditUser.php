<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('filament.resources.users.edit');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->getRecord();
        $biensWithProfile = [];
        
        foreach ($user->biens as $bien) {
            $biensWithProfile[] = [
                'bien_id' => $bien->id,
                'profile' => $bien->pivot->profile ?? 'utilisateur',
            ];
        }
        
        $data['biens_with_profile'] = $biensWithProfile;
        
        return $data;
    }
}
