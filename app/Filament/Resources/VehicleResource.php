<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->avatar()
            ->columnSpan('full'),
                Forms\Components\TextInput::make('owner')
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        1 => 'PKW',
                        2 => 'Traktor',
                        3 => 'Drescher',
                        4 => 'Pritsche',
                        5 => 'Anhänger',
                        6 => 'Pickup'
                    ])
                    ->required(),

                Forms\Components\TextInput::make('branding')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('permit'),
                Forms\Components\Textarea::make('license_plate'),
                Forms\Components\TextInput::make('insurance_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('inspection'),
                Forms\Components\TextInput::make('insurance_company')
                    ->maxLength(255),
                Forms\Components\TextInput::make('insurance_manager')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('branding'),
                Tables\Columns\TextColumn::make('image'),
                Tables\Columns\TextColumn::make('permit')
                    ->date(),
                Tables\Columns\TextColumn::make('license_plate'),
                Tables\Columns\TextColumn::make('insurance_type'),
                Tables\Columns\TextColumn::make('inspection')
                    ->date(),
                Tables\Columns\TextColumn::make('insurance_company'),
                Tables\Columns\TextColumn::make('insurance_manager'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
