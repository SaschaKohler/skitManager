<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\Placeholder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $pluralLabel = 'Artikel';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('search')
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('short_text')
                            ->columnSpan(3),

                        Forms\Components\TextInput::make('unit')
                            ->columnSpan(1),

                        Forms\Components\Fieldset::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('ek')
                                    ->reactive()
                                    ->mask(fn(Mask $mask) => $mask
                                        ->money('€')
                                        ->decimalSeparator('.')
                                        ->mapToDecimalSeparator([','])
                                        ->minValue(0)
                                    )
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('lpr')
                                    ->mask(fn(Mask $mask) => $mask
                                        ->money('€')
                                        ->decimalSeparator('.')
                                        ->mapToDecimalSeparator([','])
                                        ->minValue(0)
                                    )
                                    ->columnSpan(1),

                            ])->columns(3),

                        Forms\Components\Fieldset::make('Calculations')
                            ->schema([
                                Forms\Components\TextInput::make('vk1')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        ($state > 0) ? $set('vk1_perc', round(100 - ($get('ek') / $state * 100), 2)) : 0;
                                    })->columnSpan(2),

                                Forms\Components\TextInput::make('vk1_perc')
                                    ->reactive()
                                    ->suffix('%')
                                    ->afterStateUpdated(fn($state, callable $set, $get) => $set('vk1', round($get('ek') + $get('ek') * ($state / 100), 2)))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('vk2')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        ($state > 0) ? $set('vk2_perc', round(100 - ($get('ek') / $state * 100), 2)) : 0;
                                    })->columnSpan(2),

                                Forms\Components\TextInput::make('vk2_perc')
                                    ->reactive()
                                    ->suffix('%')
                                    ->afterStateUpdated(fn($state, callable $set, $get) => $set('vk2', round($get('ek') + $get('ek') * ($state / 100), 2)))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('vk3')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        ($state > 0) ? $set('vk3_perc', round(100 - ($get('ek') / $state * 100), 2)) : 0;
                                    })->columnSpan(2),


                                Forms\Components\TextInput::make('vk3_perc')
                                    ->reactive()
                                    ->suffix('%')
                                    ->afterStateUpdated(fn($state, callable $set, $get) => $set('vk3', round($get('ek') + $get('ek') * ($state / 100), 2)))
                                    ->columnSpan(1),


                            ])->columns(3)


                    ])->columns(4)
            ]);
    }

    public static function table(Table $table): Table
    {
        //        LIEFERANT	MATNR	KZ	SUCH	KURZTEXT	ME	EUMATLPR	EUMATEK	EUMATVK1	EUMATVK2	EUMATVK3	ZEIT	LOHNART	EULOHNSEK	EULOHNS1	EULOHNS2	EULOHNS3	ALTLIEF1	ALTLIEF2	ALTMATNR1	ALTMATNR2	CUKENNZ	CUGEWICHT	VERPEINH	MEJEVERP	PREISEINH	RABGR	HWG	WG	EANNR	ERLOESKTO	INLAGER	EUMATVK4	EULOHNS4	KALKMODE	FPREIS	SPREIS	GEAENDERT	USER	ZUSRABATT	PJVPEINH	KATALOG	USTSCHL	BESTELL	BESTELLNR	USESNR	PROABRECH	ISSKONTOF	ISUMSATZF	PEINHEIT	ARTGRUPPE	EBAY	KSTELLE	EUMATVK5	EUMATVK6	EUMATVK7	EUMATVK8	EUMATVK9	EUMATVK10	EULOHNS5	EULOHNS6	EULOHNS7	EULOHNS8	EULOHNS9	EULOHNS10	FARTIKEL	SPREISVON	SPREISBIS	ARTKAT	KRABATTGR	CANLAGER	CANRABATT	ZUSATZ_1	PEMENGE	LAGERBESTAND	LOESCHDATE	TREEKEY

        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('search')
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('short_text')
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('lpr')->money('eur'),
                Tables\Columns\TextColumn::make('ek')->money('eur'),
                Tables\Columns\TextColumn::make('vk1')->money('eur'),
//                Tables\Columns\TextColumn::make('vk2')->money('eur'),
//                Tables\Columns\TextColumn::make('vk3')->money('eur'),
            ])
            ->filters([
                Tables\Filters\Filter::make('Article')
                    ->form([
                        Forms\Components\TextInput::make('Article')
                            ->label(__('filament::resources/order-resource.table.filters.article')),

                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['Article'],
                            fn($query) => $query->where('search', 'like', strtoupper("%{$data['Article']}%")));

                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['Article'] ?? null) {
                            $indicators['Article'] = __('filament::resources/event-resource.table.filters.article');

                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
