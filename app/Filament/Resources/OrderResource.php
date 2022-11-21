<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Article;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

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


            ])
            ->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name1')
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
                Forms\Components\Placeholder::make('total_price')
                    ->content(function ($get) {
                        return collect($get('items'))->map(function ($item) {
                            return [
                                'price' => $item['qty'] * $item['unit_price'],
                            ];
                        })->sum('price');
                    }),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->registerListeners([
                        'repeater::createItem' => [
                            function (Repeater $component,): void {
                                static::calculateTransactionDetails($component->getLivewire());
                            },
                        ],
                        'repeater::deleteItem' => [
                            function (Repeater $component,): void {
                                static::calculateTransactionDetails($component->getLivewire());
                            },
                        ]
                    ])
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
                            ->reactive()
                            ->default(1)
                            ->columnSpan([
                                'md' => 1,
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->disabled()
                            ->numeric()
                            ->mask(fn(Mask $mask) => $mask
                                ->money('€')
                                ->decimalSeparator('.')
                                ->mapToDecimalSeparator([','])
                                ->minValue(0)
                            )
                            ->required()
                            ->columnSpan([
                                'md' => 2,
                            ]),
                        Forms\Components\Placeholder::make('sub_total')
                            ->disabled()
                            ->content(function ($get) {
                                return $get('qty') * $get('unit_price');
                            })
                            ->columnSpan(['md' => 2])
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

            Forms\Components\Select::make('user_id')
                ->relationship('client', 'name1')
                ->searchable()
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name1')
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(),

                    Forms\Components\TextInput::make('phone1'),
                ])
                ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                    return $action
                        ->modalHeading('Create customer')
                        ->modalButton('Create customer')
                        ->modalWidth('lg');
                }),

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

    public static function calculateTransactionDetails($component)
    {
        $price = collect($component->data['items'])->map(function ($item) {
            return [
                'price' => $item['qty'] * $item['unit_price'],
            ];
        })->sum('price');

        $component->data['total_price'] = $price;

    }
}
