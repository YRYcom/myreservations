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
        
        // Récupérer bien_id depuis la requête
        $this->bienId = request()->query('bien_id');
        
        // Pré-remplir le formulaire si bien_id est fourni
        if ($this->bienId) {
            $this->form->fill([
                'bien_id' => $this->bienId,
            ]);
        }
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // S'assurer que user_id est défini pour les utilisateurs normaux
        if (!isset($data['user_id']) || empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        
        return $data;
    }
}
