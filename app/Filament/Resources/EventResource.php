<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\ZipCode;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Projekte';

    protected static ?string $navigationLabel = 'Baustellen';
    protected static ?string $pluralLabel = 'Baustellen';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('EventData')
                    ->label(__('filament::resources/event-resource.event_data'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament::resources/event-resource.table.title'))
                            ->required(),
                        Forms\Components\Select::make('calendar_id')
                            ->label(__('filament::resources/event-resource.table.calendar_type'))
                            ->options(function () {
                                $calendars = Calendar::all();
                                return $calendars->mapWithKeys(function ($calendars) {
                                    return [$calendars->getKey() => static::getCleanOptionString($calendars)];
                                })->toArray();
                            })
                            ->required()
                            ->allowHtml()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $query) {
                                $calendar = Calendar::where('type', 'like', "%{$query}%")
                                    ->limit(50)
                                    ->get();
                                return $calendar->mapWithKeys(function ($calendar) {
                                    return [$calendar->getKey() => static::getCleanOptionString($calendar)];
                                })->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $calendar = Calendar::find($value);
                                return static::getCleanOptionString($calendar);
                            }),


                        Forms\Components\DateTimePicker::make('start')
                            ->label(__('filament::resources/event-resource.table.start'))
                            ->firstDayOfWeek(1)
                            ->withoutSeconds()
                            ->required(),
                        Forms\Components\DateTimePicker::make('end')
                            ->label(__('filament::resources/event-resource.end'))
                            ->firstDayOfWeek(1)
                            ->withoutSeconds()
                            ->required(),
                        Forms\Components\Toggle::make('allDay')->label('allDay')
                            ->label(__('filament::resources/event-resource.all_day')),
                        Forms\Components\Select::make('recurrence')
                            ->label(__('filament::resources/event-resource.recurrence'))
                            ->options([
                                '10' => 'keine',
                                '1' => 'täglich',
                                '2' => 'wöchentlich',
                                '3' => 'alle 14 Tage',
                                '4' => 'alle 3 Wochen',
                                '5' => 'monatlich',
                                '6' => 'alle 3 Monate',
                                '7' => 'halbjährlich',
                                '8' => 'jährlich',
                            ])
                            ->required(),

                        Forms\Components\Card::make()
                            ->label(__('filament::resources/event-resource.attachments'))
                            ->schema([
                                Forms\Components\FileUpload::make('images')
                                    ->label(__('filament::resources/event-resource.images'))
                                    ->multiple()
                                    ->disk('public')
                                    ->enableOpen()
                                    ->hint('max. 2MB')
                            ])->columns(1)
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Fieldset::make('Client')
                    ->label(__('filament::resources/event-resource.client_detail.header'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('filament::resources/event-resource.table.client'))
                            ->relationship('client', 'name1',
                                fn(Builder $query) => $query->where('role_id', '=', 3))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Card::make()->schema([
                            Forms\Components\Placeholder::make('Name')
                                ->label(__('filament::resources/event-resource.client_detail.name'))
                                ->content(fn(Event $record): ?string => $record->client->name1),
                            Forms\Components\Placeholder::make('email')
                                ->label(__('filament::resources/event-resource.client_detail.email'))
                                ->content(fn(Event $record): ?string => $record->client->email),
                            Forms\Components\Placeholder::make('phone1')
                                ->label(__('filament::resources/event-resource.client_detail.phone1'))
                                ->content(fn(Event $record): ?string => $record->client->phone1),
                            Forms\Components\Placeholder::make('street')
                                ->label(__('filament::resources/event-resource.client_detail.address'))
                                ->content(fn(Event $record): ?string => $record->client->street . ' / '
                                . ZipCode::find($record->client->city)?->location ?? null
                                ),
                            Forms\Components\Placeholder::make('created_at')
                                ->label(__('filament::common.created_at'))
                                ->content(fn(Event $record): ?string => $record->created_at->diffForHumans()),
                            Forms\Components\Placeholder::make('updated_at')
                                ->label(__('filament::common.updated_at'))
                                ->content(fn(Event $record): ?string => $record->updated_at->diffForHumans()),
                        ])
                            ->hidden(fn(?Event $record) => $record === null)
                            ->columns(1)
                    ])->columns(1)->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament::resources/event-resource.table.title'))
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('client.name1')
                    ->label(__('filament::resources/event-resource.table.client'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->url(fn(Event $record) => UserResource::getUrl('edit', ['record' => $record->client])),
                Tables\Columns\TextColumn::make('employees.name1')
                    ->label(__('filament::resources/event-resource.table.employees'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
                Tables\Columns\TextColumn::make('vehicles.branding')
                    ->label(__('filament::resources/event-resource.table.vehicles'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start')
                    ->label(__('filament::resources/event-resource.table.start'))
                    ->sortable()
                    ->date('d.M.y'),
                Tables\Columns\TextColumn::make('calendar.type')
                    ->label(__('filament::resources/event-resource.table.calendar_type'))


            ])
            ->filters([
                //
                Tables\Filters\Filter::make('start')
                    ->form([
                        Forms\Components\DatePicker::make('start_at')
                            ->label(__('filament::resources/event-resource.table.filters.start_at'))
                            ->default(Carbon::today()->toDateString()),
                        Forms\Components\DatePicker::make('end_at')
                            ->label(__('filament::resources/event-resource.table.filters.end_at'))
                         //   ->default(Carbon::today()->addDays(3)->toDateString()),  // user specific
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['start_at'],
                            fn($query) => $query->whereDate('start', '>=', $data['start_at']))
                            ->when($data['end_at'],
                                fn($query) => $query->whereDate('end', '<=', $data['end_at']));
                    }),
                Tables\Filters\Filter::make('calendar_id')
                    ->form([
                        Forms\Components\Select::make('calendar_id')
                            ->label(__('filament::resources/event-resource.table.calendar_type'))
                            ->options(fn() => Calendar::pluck('type', 'id'))

                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['calendar_id'],
                                fn(Builder $query, $status): Builder => $query->where('calendar_id', $data['calendar_id']));
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ]);
    }


    public
    static function getRelations(): array
    {
        return [
            RelationManagers\EmployeesRelationManager::class,
            RelationManagers\VehiclesRelationManager::class,
            RelationManagers\AddressesRelationManager::class
        ];
    }

    public
    static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    protected
    static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if ($user->role_id == 1) {
            return static::getModel()::count();
        }

        return static::getModel()::whereHas('employees', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->count();
    }

    public
    static function getEloquentQuery(): Builder
    {

        $user = auth()->user();

        if ($user->role_id == 1) {
            return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
        }

        return parent::getEloquentQuery()->whereHas('employees', function (Builder $query) {
            $query->where('user_id', auth()->id());

        })->withoutGlobalScope(SoftDeletingScope::class);
    }

    public
    static function getCleanOptionString(Model $model): string
    {
        return //Purify::clean(
            view('filament.components.select-calendar-result')
                ->with('type', $model?->type)
                ->with('description', $model?->description)
                ->with('color', $model?->color)
                ->render();

    }
}
