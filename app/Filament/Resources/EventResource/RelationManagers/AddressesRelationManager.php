<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Squire\Models\Country;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $recordTitleAttribute = 'full_address';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('street')
                    ->label(__('filament::forms/components/address-form.street')),


                Forms\Components\TextInput::make('zip')
                    ->label(__('filament::forms/components/address-form.zip')),


                Forms\Components\TextInput::make('city')
                    ->label(__('filament::forms/components/address-form.city')),


                Forms\Components\TextInput::make('manager')
                    ->label(__('filament::forms/components/address-form.manager')),


                Forms\Components\Select::make('country')
                    ->label(__('filament::forms/components/address-form.country'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn($value): ?string => Country::find($value)?->getAttribute('name')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('street')
                    ->label(__('filament::forms/components/address-form.street')),


                Tables\Columns\TextColumn::make('zip')
                    ->label(__('filament::forms/components/address-form.zip')),

                Tables\Columns\TextColumn::make('city')
                    ->label(__('filament::forms/components/address-form.city')),


                Tables\Columns\TextColumn::make('country')
                    ->label(__('filament::forms/components/address-form.country'))
                    ->formatStateUsing(fn($state): ?string => Country::find($state)?->name ?? null),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
