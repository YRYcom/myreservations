<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BienResource\Pages;
use App\Filament\Resources\BienResource\RelationManagers;
use App\Models\Bien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions;

class BienResource extends Resource
{
    protected static ?string $model = Bien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('filament.properties');
    }

    public static function getModelLabel(): string
    {
        return __('filament.properties');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label(__('filament.name')),
                Forms\Components\TextInput::make('adresse')->required()->label(__('filament.adresse')),
                Forms\Components\Textarea::make('description')->required()->label(__('filament.description')),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('biens-images')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable()->label(__('filament.name')),
                Tables\Columns\TextColumn::make('adresse')->label(__('filament.adresse')),
                Tables\Columns\TextColumn::make('description')->label(__('filament.description')),
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public'),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBien::route('/'),
            'create' => Pages\CreateBien::route('/create'),
            'edit' => Pages\EditBien::route('/{record}/edit'),
        ];
    }
}
