<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

<<<<<<< HEAD
=======
use App\Models\ZipCode;
use Closure;
>>>>>>> origin/master
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

<<<<<<< HEAD
=======
    protected static ?string $pluralModelLabel = 'zus. Adressen';


>>>>>>> origin/master
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
<<<<<<< HEAD
                Forms\Components\TextInput::make('street')
                    ->label(__('filament::forms/components/address-form.street')),


                Forms\Components\TextInput::make('zip')
                    ->label(__('filament::forms/components/address-form.zip')),


                Forms\Components\TextInput::make('city')
                    ->label(__('filament::forms/components/address-form.city')),


                Forms\Components\TextInput::make('manager')
                    ->label(__('filament::forms/components/address-form.manager')),

=======
                Forms\Components\TextInput::make('manager')
                    ->label(__('filament::forms/components/address-form.manager')),
                Forms\Components\TextInput::make('street')
                    ->label(__('filament::forms/components/address-form.street')),

                Forms\Components\Select::make('zip')
                    ->label(__('filament::common.zip'))
                    ->reactive()
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $query) => ZipCode::where('zip', 'like', "%{$query}%")->pluck('zip', 'id'))
                    ->getOptionLabelUsing(fn($value): ?string => ZipCode::find($value)?->getAttribute('zip'))
                    ->afterStateUpdated(function (Closure $set, $state) {
                        if (filled($state))
                            $set('city', ZipCode::find($state)->getAttribute('id'));
                    }),
                Forms\Components\Select::make('city')
                    ->label(__('filament::common.city'))
                    ->reactive()
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $query) => ZipCode::where('location', 'like', "%{$query}%")->pluck('location', 'id'))
                    ->getOptionLabelUsing(fn($value): ?string => ZipCode::find($value)?->getAttribute('location'))
                    ->afterStateUpdated(function (Closure $set, $state) {
                        if (filled($state))
                            $set('zip', ZipCode::find($state)->getAttribute('id'));
                    }),
>>>>>>> origin/master

                Forms\Components\Select::make('country')
                    ->label(__('filament::forms/components/address-form.country'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
<<<<<<< HEAD
                    ->getOptionLabelUsing(fn($value): ?string => Country::find($value)?->getAttribute('name')),
=======
                    ->getOptionLabelUsing(fn($value): ?string => Country::find($value)?->getAttribute('name'))
                    ->default('Österreich')
>>>>>>> origin/master
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('street')
                    ->label(__('filament::forms/components/address-form.street')),


                Tables\Columns\TextColumn::make('zip')
<<<<<<< HEAD
                    ->label(__('filament::forms/components/address-form.zip')),

                Tables\Columns\TextColumn::make('city')
                    ->label(__('filament::forms/components/address-form.city')),
=======
                    ->label(__('filament::forms/components/address-form.zip'))
                    ->getStateUsing(fn($record): ?string => ZipCode::find($record->zip)?->zip ?? null),


                Tables\Columns\TextColumn::make('city')
                    ->label(__('filament::forms/components/address-form.city'))
                    ->getStateUsing(fn($record): ?string => ZipCode::find($record->city)?->location ?? null),
>>>>>>> origin/master


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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
