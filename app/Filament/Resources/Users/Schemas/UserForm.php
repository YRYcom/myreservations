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
                        
                Select::make('biens')
                    ->label(__('filament.resources.biens.name'))
                    ->relationship('biens', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
            ]);
    }
}
