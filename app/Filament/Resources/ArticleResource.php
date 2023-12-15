<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
<<<<<<< HEAD
use App\Filament\Resources\ArticleResource\RelationManagers;
=======
>>>>>>> origin/master
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
=======
>>>>>>> origin/master

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $pluralLabel = 'Artikel';

<<<<<<< HEAD
    protected static ?string $navigationIcon = 'heroicon-o-collection';
=======
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Stammdaten';
    protected static ?int $navigationSort = 3;

>>>>>>> origin/master

    public static function form(Form $form): Form
    {
        return $form
<<<<<<< HEAD
            ->schema([
                //
                Forms\Components\TextInput::make('short_text'),
                Forms\Components\TextInput::make('unit'),
                Forms\Components\TextInput::make('lpr')
                    ->numeric()
                    ->mask(fn(Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->minValue(0)
                        ->decimalSeparator(',')
                        ->padFractionalZeros()
                    ),
                Forms\Components\TextInput::make('ek')
                    ->numeric()
                    ->mask(fn(Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator(',')
                        ->minValue(0)
                        ->padFractionalZeros()
                    ),
                Forms\Components\TextInput::make('vk1')
                    ->numeric()
                    ->mask(fn(Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator(',')
                        ->minValue(0)
                        ->padFractionalZeros()
                    ),
                Forms\Components\TextInput::make('vk2')
                    ->numeric()
                    ->mask(fn(Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator(',')
                        ->minValue(0)
                        ->padFractionalZeros()
                    ),
                Forms\Components\TextInput::make('vk3')
                    ->numeric()
                    ->mask(fn(Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator(',')
                        ->minValue(0)
                        ->padFractionalZeros()
                    ),
            ]);
=======
            ->schema(
                [
                Forms\Components\Card::make()
                    ->schema(
                        [
                        Forms\Components\TextInput::make('search')
                            ->label(__('filament::resources/article-resource.form.search'))
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('short_text')
                            ->label(__('filament::resources/article-resource.form.short_text'))
                            ->columnSpan(3),

                        Forms\Components\TextInput::make('unit')
                            ->label(__('filament::resources/article-resource.form.unit'))
                            ->columnSpan(1),

                        Forms\Components\Fieldset::make('Pricing')
                            ->label(__('filament::resources/article-resource.form.pricing'))
                            ->schema(
                                [
                                Forms\Components\TextInput::make('ek')
                                    ->label(__('filament::resources/article-resource.form.ek'))
                                    ->reactive()
                                    ->mask(
                                        fn(Mask $mask) => $mask
                                            ->money('€')
                                            ->decimalSeparator('.')
                                            ->mapToDecimalSeparator([','])
                                            ->minValue(0)
                                    )
                                    ->afterStateUpdated(
                                        function ($state, $get, $set) {
                                            if (filled($state)) {
                                                $set('vk1', round($get('ek') * $get('vk1_perc') / 100 + $get('ek'), 2));
                                                $set('vk2', round($get('ek') * $get('vk2_perc') / 100 + $get('ek'), 2));
                                                $set('vk3', round($get('ek') * $get('vk3_perc') / 100 + $get('ek'), 2));

                                            }
                                        }
                                    )
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('lpr')
                                    ->label(__('filament::resources/article-resource.form.lpr'))
                                    ->mask(
                                        fn(Mask $mask) => $mask
                                            ->money('€')
                                            ->decimalSeparator('.')
                                            ->mapToDecimalSeparator([','])
                                            ->minValue(0)
                                            ->padFractionalZeros()
                                    )
                                    ->columnSpan(1),
                                ]
                            )->columns(3),

                        Forms\Components\Fieldset::make('Calculations')
                            ->label(__('filament::resources/article-resource.form.calculations'))
                            ->schema(
                                [
                                Forms\Components\TextInput::make('vk1')
                                    ->label(__('filament::resources/article-resource.form.vk1'))
                                    ->reactive()
                                    ->disabled()
                                    ->prefix('€')
                                    ->afterStateUpdated(
                                        function ($state, callable $set, $get) {
                                            ($state > 0) ? $set('vk1_perc', round(100 - ($get('ek') / $state * 100), 2)) : 0;
                                        }
                                    )->columnSpan(2),

                                Forms\Components\TextInput::make('vk1_perc')
                                    ->label(__('filament::resources/article-resource.form.vk1_perc'))
                                    ->reactive()
                                    ->suffix('%')
                                    ->afterStateUpdated(fn($state, callable $set, $get) => $set('vk1', round($get('ek') + $get('ek') * ($state / 100), 2)))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('vk2')
                                    ->label(__('filament::resources/article-resource.form.vk2'))
                                    ->reactive()
                                    ->disabled()
                                    ->prefix('€')
                                    ->afterStateUpdated(
                                        function ($state, callable $set, $get) {
                                            ($state > 0) ? $set('vk2_perc', round(100 - ($get('ek') / $state * 100), 2)) : 0;
                                        }
                                    )->columnSpan(2),

                                Forms\Components\TextInput::make('vk2_perc')
                                    ->label(__('filament::resources/article-resource.form.vk2_perc'))
                                    ->reactive()
                                    ->suffix('%')
                                    ->afterStateUpdated(fn($state, callable $set, $get) => $set('vk2', round($get('ek') + $get('ek') * ($state / 100), 2)))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('vk3')
                                    ->label(__('filament::resources/article-resource.form.vk3'))
                                    ->reactive()
                                    ->disabled()
                                    ->prefix('€')
                                    ->afterStateUpdated(
                                        function ($state, callable $set, $get) {
                                            ($state > 0) ? $set('vk3_perc', round(100 - ($get('ek') / $state * 100), 2)) : 0;
                                        }
                                    )->columnSpan(2),


                                Forms\Components\TextInput::make('vk3_perc')
                                    ->label(__('filament::resources/article-resource.form.vk3_perc'))
                                    ->reactive()
                                    ->suffix('%')
                                    ->afterStateUpdated(fn($state, callable $set, $get) => $set('vk3', round($get('ek') + $get('ek') * ($state / 100), 2)))
                                    ->columnSpan(1),


                                ]
                            )->columns(3)


                        ]
                    )->columns(4)
                ]
            );
>>>>>>> origin/master
    }

    public static function table(Table $table): Table
    {
<<<<<<< HEAD
        //        LIEFERANT	MATNR	KZ	SUCH	KURZTEXT	ME	EUMATLPR	EUMATEK	EUMATVK1	EUMATVK2	EUMATVK3	ZEIT	LOHNART	EULOHNSEK	EULOHNS1	EULOHNS2	EULOHNS3	ALTLIEF1	ALTLIEF2	ALTMATNR1	ALTMATNR2	CUKENNZ	CUGEWICHT	VERPEINH	MEJEVERP	PREISEINH	RABGR	HWG	WG	EANNR	ERLOESKTO	INLAGER	EUMATVK4	EULOHNS4	KALKMODE	FPREIS	SPREIS	GEAENDERT	USER	ZUSRABATT	PJVPEINH	KATALOG	USTSCHL	BESTELL	BESTELLNR	USESNR	PROABRECH	ISSKONTOF	ISUMSATZF	PEINHEIT	ARTGRUPPE	EBAY	KSTELLE	EUMATVK5	EUMATVK6	EUMATVK7	EUMATVK8	EUMATVK9	EUMATVK10	EULOHNS5	EULOHNS6	EULOHNS7	EULOHNS8	EULOHNS9	EULOHNS10	FARTIKEL	SPREISVON	SPREISBIS	ARTKAT	KRABATTGR	CANLAGER	CANRABATT	ZUSATZ_1	PEMENGE	LAGERBESTAND	LOESCHDATE	TREEKEY

        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('short_text')
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('lpr'),
                Tables\Columns\TextColumn::make('ek'),
                Tables\Columns\TextColumn::make('vk1'),
                Tables\Columns\TextColumn::make('vk2'),
                Tables\Columns\TextColumn::make('vk3'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
=======
        //        LIEFERANT    MATNR    KZ    SUCH    KURZTEXT    ME    EUMATLPR    EUMATEK    EUMATVK1    EUMATVK2    EUMATVK3    ZEIT    LOHNART    EULOHNSEK    EULOHNS1    EULOHNS2    EULOHNS3    ALTLIEF1    ALTLIEF2    ALTMATNR1    ALTMATNR2    CUKENNZ    CUGEWICHT    VERPEINH    MEJEVERP    PREISEINH    RABGR    HWG    WG    EANNR    ERLOESKTO    INLAGER    EUMATVK4    EULOHNS4    KALKMODE    FPREIS    SPREIS    GEAENDERT    USER    ZUSRABATT    PJVPEINH    KATALOG    USTSCHL    BESTELL    BESTELLNR    USESNR    PROABRECH    ISSKONTOF    ISUMSATZF    PEINHEIT    ARTGRUPPE    EBAY    KSTELLE    EUMATVK5    EUMATVK6    EUMATVK7    EUMATVK8    EUMATVK9    EUMATVK10    EULOHNS5    EULOHNS6    EULOHNS7    EULOHNS8    EULOHNS9    EULOHNS10    FARTIKEL    SPREISVON    SPREISBIS    ARTKAT    KRABATTGR    CANLAGER    CANRABATT    ZUSATZ_1    PEMENGE    LAGERBESTAND    LOESCHDATE    TREEKEY

        return $table
            ->columns(
                [
                //
                Tables\Columns\TextColumn::make('search')
                    ->label(__('filament::resources/article-resource.table.search'))
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('short_text')
                    ->label(__('filament::resources/article-resource.table.short_text'))
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('unit')
                    ->label(__('filament::resources/article-resource.table.unit')),
                Tables\Columns\TextColumn::make('lpr')
                    ->label(__('filament::resources/article-resource.table.lpr'))
                    ->money('eur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ek')
                    ->label(__('filament::resources/article-resource.table.ek'))
                    ->money('eur'),
                Tables\Columns\TextColumn::make('vk1')
                    ->label(__('filament::resources/article-resource.table.vk1'))
                    ->money('eur'),
                Tables\Columns\TextColumn::make('vk2')
                    ->label(__('filament::resources/article-resource.table.vk2'))
                    ->money('eur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vk3')
                    ->label(__('filament::resources/article-resource.table.vk3'))
                    ->money('eur')
                    ->toggleable(isToggledHiddenByDefault: true),
                ]
            )
            ->filters(
                [
                Tables\Filters\Filter::make('Article')
                    ->form(
                        [
                        Forms\Components\TextInput::make('Article')
                            ->label(__('filament::resources/article-resource.table.filters.article')),

                        ]
                    )
                    ->query(
                        function ($query, array $data) {
                            return $query->when(
                                $data['Article'],
                                fn($query) => $query->where('search', 'like', strtoupper("%{$data['Article']}%"))
                            );

                        }
                    )
                    ->indicateUsing(
                        function (array $data): array {
                            $indicators = [];

                            if ($data['Article'] ?? null) {
                                $indicators['Article'] = __('filament::resources/article-resource.table.filters.article');
                            }
                            return $indicators;
                        }
                    ),
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
                ]
            );
>>>>>>> origin/master
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
