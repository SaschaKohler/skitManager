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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $label = 'Fahrzeug';
    protected static ?string $pluralLabel = 'Fahrzeuge';



    /**
     * @param string|null $label
     */
    public static function setLabel(?string $label): void
    {
        self::$label = 'Fahrzeuge';

    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->avatar()
                    ->disk('public')
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('owner')
                    ->required()
                    ->string()
                    ->maxLength(25),
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
                    ->required(),
                Forms\Components\DatePicker::make('permit')
                    ->required(),
                Forms\Components\TextInput::make('license_plate')
                    ->required(),
                Forms\Components\Select::make('insurance_type')
                    ->options([
                        1 => 'keine',
                        2 => 'Teilkasko',
                        3 => 'Vollkasko',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('inspection'),
                Forms\Components\TextInput::make('insurance_company'),
                Forms\Components\TextInput::make('insurance_manager')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->disk('public')
                ->rounded(),
                Tables\Columns\TextColumn::make('branding'),
                Tables\Columns\TextColumn::make('permit')
                    ->date(),
                Tables\Columns\TextColumn::make('license_plate'),
                Tables\Columns\TextColumn::make('inspection')
                    ->date()
                    ->sortable()

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

    public static function canView(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
