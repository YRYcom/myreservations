<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.resources.users.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('filament.resources.users.email'))
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),

                TextInput::make('password')
                    ->label(__('filament.resources.users.password'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->required(fn ($context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->autocomplete('new-password'),

                Select::make('roles')
                    ->label(__('filament.resources.users.role'))
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('filament.roles_required'),
                    ]),
                        
                \Filament\Forms\Components\Repeater::make('biens_with_profile')
                    ->label(__('filament.resources.users.biens'))
                    ->schema([
                        Select::make('bien_id')
                            ->label(__('filament.resources.users.biens.select'))
                            ->options(\App\Models\Bien::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        Select::make('profile')
                            ->label(__('filament.resources.users.profile'))
                            ->options(\App\Enums\ProfileType::options())
                            ->default('utilisateur')
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel(__('filament.resources.users.biens.add'))
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        return [
                            'bien_id' => $data['id'],
                            'profile' => $data['pivot']['profile'] ?? 'utilisateur',
                        ];
                    })
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        return $data;
                    })
                    ->saveRelationshipsUsing(function ($component, $state, $record) {
                        if (!$record) {
                            return;
                        }
                        
                        $syncData = [];
                        foreach ($state ?? [] as $item) {
                            if (isset($item['bien_id'])) {
                                $syncData[$item['bien_id']] = [
                                    'profile' => $item['profile'] ?? 'utilisateur'
                                ];
                            }
                        }
                        
                        $record->biens()->sync($syncData);
                    })
                    ->dehydrated(false)
            ]);
    }
}
