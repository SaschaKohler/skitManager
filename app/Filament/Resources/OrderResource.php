<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Article;
use App\Models\Order;
use App\Models\User;
use App\Models\ZipCode;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Buchführung';

    protected static ?string $pluralLabel = 'Rechnungen';


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

                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\Placeholder::make('total')
                                    ->label(__('filament::resources/order-resource.form.total'))
                                    ->reactive()
                                    ->content(function (callable $get) {
                                        if ($get('discount'))
                                            return number_format(array_sum(data_get($get('items'), '*.sub_total')) - array_sum(data_get($get('items'), '*.sub_total')) * $get('discount') / 100, 2) . ' €';
                                        return number_format(array_sum(data_get($get('items'), '*.sub_total')), 2) . ' €';

                                    })->extraAttributes(['class' => 'text-2xl text-bold'])
                                    ->columnSpan(1),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label(__('filament::resources/order-resource.created_at'))
                                    ->content(fn(Order $record): ?string => $record->created_at?->diffForHumans())
                                    ->columnSpan(1),
                                Forms\Components\Placeholder::make('updated_at')
                                    ->label(__('filament::resources/order-resource.updated_at'))
                                    ->content(fn(Order $record): ?string => $record->updated_at?->diffForHumans())
                                    ->columnSpan(1),
                            ])
                            ->columns(['lg' => 3])
                            ->hidden(fn(?Order $record) => $record === null),


                        Forms\Components\Section::make(__('filament::resources/order-resource.form.order_items'))
                            ->schema(static::getFormSchema('items')),
                    ])
                    ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 8 : 8]),


            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('number')
                    ->label(__('filament::resources/order-resource.table.number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name1')
                    ->label(__('filament::resources/order-resource.table.client_name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('filament::resources/order-resource.table.status'))
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn($state) => in_array($state, ['delivered', 'shipped']),
                    ]),

                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('filament::resources/order-resource.table.total_price'))
                    ->money('eur')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament::resources/order-resource.table.order_created_at'))
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
                Forms\Components\TextInput::make('discount')
                    ->label(__('filament::resources/order-resource.form.total_discount'))
                    ->reactive()
                    ->numeric()
                    ->suffix('%'),


                Forms\Components\Repeater::make('items')
                    ->label(__('filament::resources/order-resource.form.items'))
                    ->relationship()
//                    ->registerListeners([
//                        'repeater::createItem' => [
//                            function (Repeater $component,): void {
//                                static::calculateTransactionDetails($component->getLivewire());
//                            },
//                        ],
//                        'repeater::deleteItem' => [
//                            function (Repeater $component,): void {
//                                static::calculateTransactionDetails($component->getLivewire());
//                            },
//                        ]
//                    ])
                    ->schema([

                        Forms\Components\Select::make('article_id')
                            ->label(__('filament::resources/order-resource.form.article'))
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(fn(string $query) => Article::where('search', 'like', mb_strtoupper("%{$query}%", 'UTF-8'))->pluck('short_text', 'id'))
                            ->getOptionLabelUsing(fn($value): ?string => Article::find($value)?->getAttribute('short_text'))
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $set('unit_price', Article::find($state)?->vk1 ?? 0);
                                $set('unit', Article::find($state)?->unit ?? 0);
                                $set('sub_total', $get('qty') * $get('unit_price'));
                            })
                            ->columnSpan([
                                'md' => 10,
                            ]),
                        Forms\Components\TextInput::make('discount')
                            ->label(__('filament::resources/order-resource.form.row_discount'))
                            ->reactive()
                            ->suffix('%')
                            ->columnSpan(['md' => 3])
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                filled($state) ?
                                    $set('sub_total', round($get('sub_total') - $state / 100 * $get('sub_total'), 2)) :
                                    $set('sub_total', round($get('qty') * $get('unit_price'), 2));
                            }),

                        Forms\Components\TextInput::make('qty')
                            ->label(__('filament::resources/order-resource.form.qty'))
                            ->numeric()
                            ->reactive()
                            ->default(1)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                filled($state) ? $set('sub_total', round($get('qty') * $get('unit_price'), 2)) : 0;
                            })
                            ->columnSpan([
                                'md' => 1,
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('unit')
                            ->label(__('filament::resources/order-resource.form.unit'))
                            ->reactive()
                            ->disabled()
                            ->columnSpan(['md' => 1]),

                        Forms\Components\TextInput::make('unit_price')
                            ->label(__('filament::resources/order-resource.form.unit_price'))
                            ->disabled()
                            ->reactive()
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
                        Forms\Components\TextInput::make('sub_total')
                            ->label(__('filament::resources/order-resource.form.sub_total'))
                            ->disabled()
                            ->reactive()
                            ->numeric()
                            ->columnSpan(['md' => 3])
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
                ->label(__('filament::resources/order-resource.form.number'))
                ->default('OR-' . random_int(100000, 999999))
                ->disabled()
                ->required(),
            Forms\Components\Select::make('status')
                ->label(__('filament::resources/order-resource.table.status'))
                ->options([
                    'new' => __('filament::resources/order-resource.form.status.options.new'),
                    'processing' => __('filament::resources/order-resource.form.status.options.processing'),
                    'delivered' => __('filament::resources/order-resource.form.status.options.delivered'),
                    'cancelled' => __('filament::resources/order-resource.form.status.options.cancelled'),
                ])
                ->required(),

Forms\Components\Card::make()
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('filament::resources/order-resource.form.client_name'))
                    ->relationship('client', 'name1')
                    ->searchable()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('street', User::find($state)?->street ?? 0);
                        $set('zip', ZipCode::find(User::find($state)?->zip)?->getAttribute('zip') ?? 0);
                        $set('city', ZipCode::find(User::find($state)?->city)?->getAttribute('location') ?? 0);
                    }),
//                  ->createOptionForm([
//                    Forms\Components\TextInput::make('name1')
//                        ->required(),
//
//                    Forms\Components\TextInput::make('email')
//                        ->required()
//                        ->email(),
////                        ->unique(),
//
//                    Forms\Components\TextInput::make('phone1'),
//                ])
//                ->createOptionAction(function (Forms\Components\Actions\Action $action) {
//                    return $action
//                        ->modalHeading('Create customer')
//                        ->modalButton('Create customer')
//                        ->modalWidth('lg');
//                }),
                Forms\Components\TextInput::make('street')
                    ->disabled(),
                Forms\Components\TextInput::make('zip')
                    ->disabled(),
                Forms\Components\TextInput::make('city')
                    ->disabled(),

            ])

//
//            AddressForm::make('address')
//                ->columnSpan('full'),
//
//            Forms\Components\MarkdownEditor::make('notes')
//                ->columnSpan('full'),
        ];
    }

//    public static function calculateTransactionDetails($component)
//    {
//        $price = collect($component->data['items'])->map(function ($item) {
//            return [
//                'price' => $item['qty'] * $item['unit_price'],
//            ];
//        })->sum('price');
//
//        $component->data['total_price'] = $price;
//
//    }

    protected
    static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
