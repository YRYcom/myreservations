<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        $reservation = $this->getRecord();
        $user = auth()->user();
        
        $actions = [];
        
        if ($user && $user->hasRole('admin')) {
            $actions[] = DeleteAction::make();
        }

        
        if ($reservation && $user && $reservation->canBeApprovedBy($user)) {
            $actions[] = \Filament\Actions\Action::make('approve')
                ->label(__('filament.resources.reservations.approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($reservation->status, [
                    \App\Enums\ReservationStatus::EnAttente,
                    \App\Enums\ReservationStatus::Refuse
                ]))
                ->form([
                    \Filament\Forms\Components\Textarea::make('comment')
                        ->label(__('filament.resources.reservations.approval_comment'))
                        ->placeholder(__('filament.resources.reservations.approval_comment_placeholder'))
                        ->rows(3),
                ])
                ->action(function (array $data) use ($reservation, $user) {
                    $reservation->approve($data['comment'] ?? null, $user->id);
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('filament.enums.reservation_status.accepte'))
                        ->send();
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $reservation]));
                });
            
            $actions[] = \Filament\Actions\Action::make('reject')
                ->label(__('filament.resources.reservations.reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => in_array($reservation->status, [
                    \App\Enums\ReservationStatus::EnAttente,
                    \App\Enums\ReservationStatus::Accepte
                ]))
                ->form([
                    \Filament\Forms\Components\Textarea::make('comment')
                        ->label(__('filament.resources.reservations.approval_comment'))
                        ->placeholder(__('filament.resources.reservations.approval_comment_placeholder'))
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (array $data) use ($reservation, $user) {
                    $reservation->reject($data['comment'] ?? null, $user->id);
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('filament.enums.reservation_status.refuse'))
                        ->send();
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $reservation]));
                });
            
            $actions[] = \Filament\Actions\Action::make('reset_to_pending')
                ->label(__('filament.resources.reservations.reset_to_pending'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => in_array($reservation->status, [
                    \App\Enums\ReservationStatus::Accepte,
                    \App\Enums\ReservationStatus::Refuse
                ]))
                ->form([
                    \Filament\Forms\Components\Textarea::make('comment')
                        ->label(__('filament.resources.reservations.approval_comment'))
                        ->placeholder(__('filament.resources.reservations.approval_comment_placeholder'))
                        ->rows(3),
                ])
                ->action(function (array $data) use ($reservation, $user) {
                    $reservation->resetToPending($data['comment'] ?? null, $user->id);
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('filament.enums.reservation_status.en_attente'))
                        ->send();
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $reservation]));
                });
        }
        
        return $actions;
    }

    public function getTitle(): string
    {
        return __('filament.resources.reservations.edit');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        
        if ($record && 
            ($record->date_start != $data['date_start'] || $record->date_end != $data['date_end'])) {
            $data['status'] = \App\Enums\ReservationStatus::EnAttente->value;
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $originalData = $record->getOriginal();
        
        if ($originalData['date_start'] != $record->date_start || 
            $originalData['date_end'] != $record->date_end) {
            if ($record->status === \App\Enums\ReservationStatus::EnAttente) {
                $record->logStatusChange(
                    \App\Enums\ReservationStatus::EnAttente,
                    'Dates modifiées - réinitialisation du statut',
                    null
                );
            }
        }
    }
}
