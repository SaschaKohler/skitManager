<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Forms\Components\AddressForm;
use App\Models\Article;
use App\Models\Order;
use App\Models\Shop\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema(static::getFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make('Order items')
                            ->schema(static::getFormSchema('items')),
                    ])
                    ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 3 : 2]),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn(Order $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn(Order $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Order $record) => $record === null),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.client.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn($state) => in_array($state, ['delivered', 'shipped']),
                    ]),

                Tables\Columns\TextColumn::make('total_price')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->toggleable(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getFormSchema(?string $section = null): array
    {
        if ($section === 'items') {
            return [
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('article_id')
                            ->label('Article')
                            ->options(Article::query()->pluck('short_text', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('unit_price', Article::find($state)?->vk1 ?? 0))
                            ->columnSpan([
                                'md' => 5,
                            ]),

                        Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->default(1)
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->disabled()
                            ->numeric()
                            ->required()
                            ->columnSpan([
                                'md' => 3,
                            ]),
                    ])
                    ->orderable()
                    ->defaultItems(1)
                    ->disableLabel()
                    ->columns([
                        'md' => 10,
                    ])
                    ->required(),
            ];
        }

        return [
            Forms\Components\TextInput::make('number')
                ->default('OR-' . random_int(100000, 999999))
                ->disabled()
                ->required(),

            Forms\Components\Select::make('event_id')
                ->relationship('event', 'title')
                ->searchable()
                ->required(),
//                ->createOptionForm([
//                    Forms\Components\TextInput::make('name')
//                        ->required(),
//
//                    Forms\Components\TextInput::make('email')
//                        ->required()
//                        ->email()
//                        ->unique(),
//
//                    Forms\Components\TextInput::make('phone'),
//                ])
//                ->createOptionAction(function (Forms\Components\Actions\Action $action) {
//                    return $action
//                        ->modalHeading('Create customer')
//                        ->modalButton('Create customer')
//                        ->modalWidth('lg');
//                }),

            Forms\Components\Select::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),

//            Forms\Components\Select::make('currency')
//                ->searchable()
//                ->getSearchResultsUsing(fn (string $query) => Currency::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
//                ->getOptionLabelUsing(fn ($value): ?string => Currency::find($value)?->getAttribute('name'))
//                ->required(),
//
//            AddressForm::make('address')
//                ->columnSpan('full'),
//
//            Forms\Components\MarkdownEditor::make('notes')
//                ->columnSpan('full'),
        ];
    }
}
