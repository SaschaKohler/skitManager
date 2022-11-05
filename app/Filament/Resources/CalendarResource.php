<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarResource\Pages;
use App\Filament\Resources\CalendarResource\RelationManagers;
use App\Models\Calendar;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalendarResource extends Resource
{
    protected static ?string $model = Calendar::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $label = 'Kategorie (Farbe)';
    protected static ?string $pluralLabel = 'Kategorien (Farben)';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->label(__('filament::resources/calendar-resource.type'))
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label(__('filament::resources/calendar-resource.description')),

                        Forms\Components\ColorPicker::make('color')
                            ->label(__('filament::resources/calendar-resource.color'))
                            ->rgb()
                            ->required(),

                    ])->columnSpan(['lg' => 2]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament::resources/calendar-resource.type')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament::resources/calendar-resource.description')),
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('filament::resources/calendar-resource.color'))
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
            'index' => Pages\ListCalendars::route('/'),
            'create' => Pages\CreateCalendar::route('/create'),
            'edit' => Pages\EditCalendar::route('/{record}/edit'),
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
