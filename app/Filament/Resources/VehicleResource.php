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

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    //    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationGroup = 'Stammdaten';

    protected static ?string $label = 'Fahrzeug';
    protected static ?string $pluralLabel = 'Fahrzeuge';


    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                Forms\Components\FileUpload::make('image')
                    ->avatar()
                    ->disk('public')
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('owner')
                    ->required()
                    ->string()
                    ->maxLength(25),
                Forms\Components\Select::make('type')
                    ->options(
                        [
                        1 => 'PKW',
                        2 => 'Traktor',
                        3 => 'Drescher',
                        4 => 'Pritsche',
                        5 => 'Anhänger',
                        6 => 'Pickup'
                        ]
                    )
                    ->required(),
                Forms\Components\Card::make()
                    ->schema(
                        [
                        Forms\Components\FileUpload::make('image')
                            ->label(__('filament::resources/vehicle-resource.image'))
                            ->avatar()
                            ->disk('public')
                            ->columnSpan(4),
                        Forms\Components\TextInput::make('branding')
                            ->label(__('filament::resources/vehicle-resource.branding'))
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('license_plate')
                            ->label(__('filament::resources/vehicle-resource.license_plate'))
                            ->required()
                            ->columns(1),


                        Forms\Components\Fieldset::make('details')
                            ->label(__('filament::resources/vehicle-resource.details'))
                            ->schema(
                                [
                                Forms\Components\Select::make('type')
                                    ->label(__('filament::resources/vehicle-resource.type'))
                                    ->options(
                                        [
                                        1 => __('filament::resources/vehicle-resource.type_options.pkw'),
                                        2 => __('filament::resources/vehicle-resource.type_options.traktor'),
                                        3 => __('filament::resources/vehicle-resource.type_options.drescher'),
                                        4 => __('filament::resources/vehicle-resource.type_options.pritsche'),
                                        5 => __('filament::resources/vehicle-resource.type_options.anhaenger'),
                                        6 => __('filament::resources/vehicle-resource.type_options.pickup'),
                                        ]
                                    )
                                    ->required()
                                    ->columns(3),
                                Forms\Components\TextInput::make('owner')
                                    ->label(__('filament::resources/vehicle-resource.owner'))
                                    ->required()
                                    ->string()
                                    ->maxLength(25)
                                    ->columns(2),


                                Forms\Components\DatePicker::make('permit')
                                    ->label(__('filament::resources/vehicle-resource.permit'))
                                    ->required(),
                                Forms\Components\Select::make('insurance_type')
                                    ->label(__('filament::resources/vehicle-resource.insurance_type'))
                                    ->options(
                                        [
                                        1 => 'keine',
                                        2 => 'Teilkasko',
                                        3 => 'Vollkasko',
                                        ]
                                    )
                                    ->required(),
                                Forms\Components\DatePicker::make('inspection')
                                    ->label(__('filament::resources/vehicle-resource.inspection')),
                                Forms\Components\TextInput::make('insurance_company')
                                    ->label(__('filament::resources/vehicle-resource.insurance_company')),
                                Forms\Components\TextInput::make('insurance_manager')
                                    ->label(__('filament::resources/vehicle-resource.insurance_manager')),

                                ]
                            )->columnSpan(4)


                        ]
                    )
                ]
            )->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->rounded(),
                Tables\Columns\TextColumn::make('branding')
                    ->label(__('filament::resources/vehicle-resource.image'))
                    ->disk('public')
                    ->rounded(),
                Tables\Columns\TextColumn::make('branding')
                    ->label(__('filament::resources/vehicle-resource.branding'))
                    ->wrap(),
                Tables\Columns\TextColumn::make('permit')
                    ->label(__('filament::resources/vehicle-resource.permit'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('license_plate')
                    ->label(__('filament::resources/vehicle-resource.license_plate')),
                Tables\Columns\TextColumn::make('inspection')
                    ->label(__('filament::resources/vehicle-resource.inspection'))
                    ->date()
                    ->sortable()

                ]
            )
            ->filters(
                [
                Tables\Filters\TrashedFilter::make(),
                ]
            )
            ->actions(
                [
                Tables\Actions\EditAction::make(),
                ]
            )
            ->bulkActions(
                [
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                ]
            );
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
            ->withoutGlobalScopes(
                [
                SoftDeletingScope::class,
                ]
            );
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
