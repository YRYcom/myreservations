<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;
    
    public ?string $bienId = null;
    
    public function mount(): void
    {
        parent::mount();
        
        $this->bienId = request()->query('bien_id');
        
        if ($this->bienId) {
            $this->form->fill([
                'bien_id' => $this->bienId,
            ]);
        }
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['user_id']) || empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        
        $data['status'] = \App\Enums\ReservationStatus::EnAttente->value;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $reservation = $this->getRecord();
        
        $reservation->logStatusChange(
            \App\Enums\ReservationStatus::EnAttente,
            'Création de la réservation',
            null
        );
        
        \Illuminate\Support\Facades\Mail::to($reservation->user->email)
            ->send(new \App\Mail\ReservationPendingUserNotification($reservation));
        
        $managers = $reservation->bien->users()
            ->wherePivot('profile', 'gestionnaire')
            ->get();
        
        foreach ($managers as $manager) {
            \Illuminate\Support\Facades\Mail::to($manager->email)
                ->send(new \App\Mail\ReservationPendingManagerNotification($reservation));
        }
    }

    public function getTitle(): string
    {
        return __('filament.resources.reservations.create');
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
}
