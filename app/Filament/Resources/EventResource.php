<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Calendar;
use App\Models\Event;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Events';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('EventData')
                    ->label(__('filament::resources/user-resource.event_data'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament::resources/user-resource.title'))
                            ->required(),
                        Forms\Components\Select::make('calendar_id')
                            ->label(__('filament::resources/user-resource.calendar_type'))
                            ->relationship('calendar', 'type'),
                        Forms\Components\DateTimePicker::make('start')
                            ->label(__('filament::resources/user-resource.start'))
                            ->firstDayOfWeek(1)
                            ->withoutSeconds()
                            ->required(),
                        Forms\Components\DateTimePicker::make('end')
                            ->label(__('filament::resources/user-resource.end'))
                            ->firstDayOfWeek(1)
                            ->withoutSeconds()
                            ->required(),
                        Forms\Components\Toggle::make('allDay')->label('allDay')
                            ->label(__('filament::resources/user-resource.all_day')),
                        Forms\Components\Select::make('recurrence')
                            ->label(__('filament::resources/user-resource.recurrence'))
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
                            ->label(__('filament::resources/user-resource.attachments'))
                            ->schema([
                                Forms\Components\FileUpload::make('images')
                                    ->label(__('filament::resources/user-resource.images'))
                                    ->multiple()
                                    ->disk('public')
                                    ->enableOpen()
                            ])->columns(1)
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Fieldset::make('Client')
                    ->label(__('filament::resources/user-resource.client_detail'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('filament::resources/user-resource.client'))
                            ->relationship('client', 'name1',
                                fn(Builder $query) => $query->where('role_id', '=', 3))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Card::make()->schema([
                            Forms\Components\Placeholder::make('Name')
                                ->content(fn(Event $record): string => $record->client->name1),
                            Forms\Components\Placeholder::make('email')
                                ->content(fn(Event $record): string => $record->client->email),
                            Forms\Components\Placeholder::make('phone1')
                                ->content(fn(Event $record): string => $record->client->phone1),
                            Forms\Components\Placeholder::make('street')
                                ->content(fn(Event $record): string => $record->client->street . ' ' . $record->client->city),
                            Forms\Components\Placeholder::make('created_at')
                                ->content(fn(Event $record): string => $record->created_at->diffForHumans()),
                            Forms\Components\Placeholder::make('updated_at')
                                ->content(fn(Event $record): string => $record->updated_at->diffForHumans()),
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
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('client.name1')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->url(fn(Event $record) => UserResource::getUrl('edit', ['record' => $record->client])),
                Tables\Columns\TextColumn::make('employees.name1')
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('vehicles.branding')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start')
                    ->date('d.M.y'),
                Tables\Columns\TextColumn::make('calendar.type'),

            ])
            ->filters([
                //
                Tables\Filters\Filter::make('start')
                    ->form([
                        Forms\Components\DatePicker::make('start_at'),
                        Forms\Components\DatePicker::make('end_at')
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

//    public
//    static function getWidgets(): array
//    {
//        return [
//    //        EventResource\Widgets\CalendarWidget::class,
//        ];
//    }

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

}
