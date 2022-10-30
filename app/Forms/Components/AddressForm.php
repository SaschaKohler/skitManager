<?php

namespace App\Forms\Components;

use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Squire\Models\Country;

class AddressForm extends Forms\Components\Field
{
    protected string $view = 'forms::components.group';

    public $relationship = null;

    public function relationship(string | callable $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function saveRelationships(): void
    {
        $state = $this->getState();
        $record = $this->getRecord();
        $relationship = $record->{$this->getRelationship()}();

        if ($address = $relationship->first()) {
            $address->update($state);
        } else {
            $relationship->updateOrCreate($state);
        }

        $record->touch();
    }

    public function getChildComponents(): array
    {
        return [
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Select::make('country')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Country::find($value)?->getAttribute('name')),
                ]),
            Forms\Components\TextInput::make('street')
                ->label(__('filament::forms/components/address-form.street')),
            Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\TextInput::make('manager'),
                    Forms\Components\TextInput::make('zip')
                        ->label('Zip'),
                    Forms\Components\TextInput::make('city')
                        ->label('CIty'),
                ]),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (AddressForm $component, ?Model $record) {
            $address = $record?->getRelationValue($this->getRelationship());

            $component->state($address ? $address->toArray() : [
                'street' => null,
                'zip' => null,
                'city' => null,
                'country' => null,
                'manager' => null,
            ]);
        });

        $this->dehydrated(false);
    }

    public function getRelationship(): string
    {
        return $this->evaluate($this->relationship) ?? $this->getName();
    }
}
