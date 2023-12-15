<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Filament\Resources\VehicleResource;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    protected static ?string $recordTitleAttribute = 'branding';

    protected static ?string $inverseRelationship = 'events'; // Since the inverse related model is `Category`, this is normally `category`, not `section`.

    protected static ?string $pluralLabel = 'Fahrzeuge';


    public static function form(Form $form): Form
    {
        return VehicleResource::form($form);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branding'),
            ])
            ->filters([
                //
            ])
            ->headerActions(actions: [
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(true)
                    ->recordSelect(function (Select $select, VehiclesRelationManager $livewire) {
                        $event = $livewire->getRelationship()->getParent();
                        $excluded = [... $event->vehicles->pluck('branding')];
                        $select->options(Vehicle::whereNotIn('branding', $excluded)->pluck('branding', 'id'));
                        return
                            $select->getSearchResultsUsing(function ($search) use ($excluded) {
                                return
                                    Vehicle::query()
                                        ->whereNotIn('branding', $excluded)
                                        ->where('branding', 'like', "%{$search}%")
                                        ->pluck('branding', 'id');
                            });
                    })

            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make()

            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);


    }
}
