<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
    }

    public static function table(Table $table): Table
    {
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
