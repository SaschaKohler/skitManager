<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Filament\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Event;
use Closure;
use DateInterval;
use DateTime;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

use Illuminate\Support\Facades\DB;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static string $view = 'filament.resources.event-resource.widgets.calendar-widget';


//    public function getViewData(): array
//    {
//  //      return Event::all()->toArray();
//        dd();
//        return [];
////
//    }

    public function fetchEvents(array $fetchInfo): array
    {
        $user = auth()->user();

        if ($user->role_id == 2) {

            // You can use $fetchInfo to filter events by date.

            return Event::query()
                ->where([
                    ['start', '>=', $fetchInfo['start']],
                    ['end', '<=', $fetchInfo['end']],
                ])
                ->whereHas('employees', function (Builder $query) {
                    $query->where('user_id', auth()->id());
                })
                ->get()
                ->toArray();
        }
        return Event::query()
            ->where([
                ['start', '>=', $fetchInfo['start']],
                ['end', '<=', $fetchInfo['end']],
            ])
            ->get()
            ->toArray();

    }

    protected function getFormModel(): Model|string|null
    {
        return $this->event ?? Event::class;
    }

    public function onEventClick($event): void
    {
        parent::onEventClick($event);


        // your code
        //   $this->editEventForm->model($this->event);
        $event = Event::find($event['id']);


    }

    public function onEventDrop($newEvent, $oldEvent, $relatedEvents): void
    {

        $this->event = Event::find($newEvent['id']);

        if (!array_key_exists('end', $newEvent)) {
            $dt = DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $newEvent['start']);
            if ($dt) {
                $dt->add(new DateInterval('PT1H'));
                $newEvent['end'] = $dt->format('Y-m-d\TH:i:s\Z');
                $newEvent['allDay'] = false;
                $newEvent['extendedProps']['allDay'] = false;
            } else {
                $newEvent['allDay'] = true;
                $newEvent['extendedProps']['allDay'] = true;

            }
        }
        $this->event->update($newEvent);
        $this->refreshEvents();

    }

    /**
     * Triggered when event's resize stops.
     */
    public function onEventResize($event, $oldEvent, $relatedEvents): void
    {
        // your code
        $this->event = $this->resolveEventRecord($event);
        $this->event->update($event);
        $this->refreshEvents();

    }

    public function createEvent(array $event): void
    {
        // Create the event with the provided $data.
        $data = $event;
        $data['user_id'] = $event['extendedProps']['user_id'];
        $data['allDay'] = $event['extendedProps']['allDay'];
        $data['calendar_id'] = $event['extendedProps']['calendar_id'];
       // $data['backgroundColor'] = $event['extendedProps']['backgroundColor'];
       // $data['borderColor'] = $event['extendedProps']['backgroundColor'];
        $data['recurrence'] = $data['extendedProps']['recurrence'];


        $this->event = Event::create($data);
        $this->event->backgroundColor = $this->event->calendar->color;
        $this->event->borderColor = $this->event->calendar->color;
        $this->event->update();
        $this->refreshEvents();

        Notification::make()
            ->title('Neuer Eintrag')
            //->icon('heroicon-s-calender')
            ->body("**{$this->event->title} am {$this->event->start}**")
            ->actions([
                Action::make('View')
                    ->url(EventResource::getUrl('edit', ['record' => $this->event])),
            ])
            ->sendToDatabase(auth()->user());

    }


    public function editEvent(array $data): void
    {
        // Edit the event with the provided $data.

        //  dd($data);

        /**
         * here you can access to 2 properties to perform update
         * 1. $this->event_id
         * 2. $this->event
         */

        # $this->event_id
        // the value is retrieved from event's id key
        // eg: Appointment::find($this->event);

        # $this->event
        // model instance is resolved by user defined resolveEventRecord() funtion. See example below
        $dat = $data;
        $dat['allDay'] = $data['extendedProps']['allDay'];
        $dat['calendar_id'] = (int) $data['extendedProps']['calendar_id'];
        $dat['recurrence'] = $data['extendedProps']['recurrence'];

      //  $this->event->
        $this->event->update($dat);
        $this->event->backgroundColor = $this->event->calendar()->pluck('color')[0];
        $this->event->borderColor = $this->event->calendar()->pluck('color')[0];
        $this->event->update();

        $this->refreshEvents();

    }

    public function resolveEventRecord(array $data): Model
    {
        // Using Appointment class as example
        return Event::find($data['id']);
    }

    protected static function getCreateEventFormSchema(): array
    {
        return [
<<<<<<< HEAD
            Forms\Components\TextInput::make('title')
                ->required(),
            Forms\Components\DateTimePicker::make('start')
                ->withoutSeconds()
                ->required(),
            Forms\Components\DateTimePicker::make('end')
                ->withoutSeconds()
                ->required(),
            Forms\Components\Toggle::make('extendedProps.allDay'),
            Forms\Components\Select::make('extendedProps.recurrence')
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
=======
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Toggle::make('extendedProps.allDay')
                        ->label(__('filament::widgets/calendar-widget.allday'))
                        ->columnSpan(2),
                    Forms\Components\Select::make('extendedProps.calendar_id')
                        ->disableLabel()
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
                        })
                        ->columnSpan(2),

                ])->columns(4),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('filament::widgets/calendar-widget.title'))
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\DateTimePicker::make('start')
                        ->label(__('filament::widgets/calendar-widget.start'))
                        ->withoutSeconds()
                        ->required()
                        ->columnSpan(2),
                    Forms\Components\DateTimePicker::make('end')
                        ->label(__('filament::widgets/calendar-widget.end'))
                        ->withoutSeconds()
                        ->required()
                        ->columnSpan(2),

                ])->columns('4'),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Select::make('extendedProps.recurrence')
                        ->label(__('filament::widgets/calendar-widget.recurrence.header'))
                        ->options([
                            '10' =>__('filament::widgets/calendar-widget.recurrence.none'),
                            '1' => __('filament::widgets/calendar-widget.recurrence.daily'),
                            '2' => __('filament::widgets/calendar-widget.recurrence.weekly'),
                            '3' => __('filament::widgets/calendar-widget.recurrence.14day'),
                            '4' => __('filament::widgets/calendar-widget.recurrence.3week'),
                            '5' => __('filament::widgets/calendar-widget.recurrence.monthly'),
                            '6' => __('filament::widgets/calendar-widget.recurrence.3month'),
                            '7' => __('filament::widgets/calendar-widget.recurrence.halfyear'),
                            '8' => __('filament::widgets/calendar-widget.recurrence.yearly'),
                        ])
                        ->required()
                        ->allowHtml(),

                    Forms\Components\Select::make('extendedProps.user_id')
                        ->label(__('filament::widgets/calendar-widget.user_id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            return User::where('role_id', 3)->pluck('name1', 'id');
                        })
                        ->getSearchResultsUsing(function ($query){
                            return User::where('name1', 'like', "%{$query}%")->
                            where('role_id', 3)->pluck('name1', 'id');
                        })

                ])

        ];

    }

    protected
    static function getEditEventFormSchema(): array
    {

        return [
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Toggle::make('extendedProps.allDay')
                        ->label(__('filament::widgets/calendar-widget.allday'))
                        ->columnSpan(2),
                    Forms\Components\Select::make('extendedProps.calendar_id')
                        ->disableLabel()
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
                        })
                        ->columnSpan(2),

                ])->columns(4),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('filament::widgets/calendar-widget.title'))
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\DateTimePicker::make('start')
                        ->label(__('filament::widgets/calendar-widget.start'))
                        ->withoutSeconds()
                        ->required()
                        ->columnSpan(2),
                    Forms\Components\DateTimePicker::make('end')
                        ->label(__('filament::widgets/calendar-widget.end'))
                        ->withoutSeconds()
                        ->required()
                        ->columnSpan(2),

                ])->columns('4'),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Select::make('extendedProps.recurrence')
                        ->label(__('filament::widgets/calendar-widget.recurrence.header'))
                        ->options([
                            '10' =>__('filament::widgets/calendar-widget.recurrence.none'),
                            '1' => __('filament::widgets/calendar-widget.recurrence.daily'),
                            '2' => __('filament::widgets/calendar-widget.recurrence.weekly'),
                            '3' => __('filament::widgets/calendar-widget.recurrence.14day'),
                            '4' => __('filament::widgets/calendar-widget.recurrence.3week'),
                            '5' => __('filament::widgets/calendar-widget.recurrence.monthly'),
                            '6' => __('filament::widgets/calendar-widget.recurrence.3month'),
                            '7' => __('filament::widgets/calendar-widget.recurrence.halfyear'),
                            '8' => __('filament::widgets/calendar-widget.recurrence.yearly'),
                        ])
                        ->required()
                        ->allowHtml(),

                    Forms\Components\Select::make('extendedProps.user_id')
                        ->label(__('filament::widgets/calendar-widget.user_id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            return User::where('role_id', 3)->pluck('name1', 'id');
                        })
                        ->getSearchResultsUsing(function ($query) {
                            return User::where('name1', 'like', "%{$query}%")->
                            where('role_id', 3)->pluck('name1', 'id');
                        })

>>>>>>> b0bf549 (start translations)
                ])
                ->required()
                ->allowHtml()
                ->view('filament.select'),
            Forms\Components\Select::make('extendedProps.calendar_id')
                ->relationship('calendar', 'type',)
                ->model(Event::class)
                ->required(),

            Forms\Components\Select::make('extendedProps.user_id')
                ->relationship('client', 'search')
                ->model(Event::class)
                ->required()

        ];
    }

    protected static function getEditEventFormSchema(): array
    {

        return [
            Forms\Components\TextInput::make('title')
                ->required(),
            Forms\Components\DateTimePicker::make('start')
                ->required()->withoutSeconds(),
            Forms\Components\DateTimePicker::make('end')
                ->required()->withoutSeconds(),
            Forms\Components\Toggle::make('extendedProps.allDay'),
            Forms\Components\Select::make('extendedProps.recurrence')
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

            Forms\Components\Select::make('extendedProps.calendar_id')
                ->model(Event::class)
                ->relationship('calendar','type'),

            Forms\Components\Select::make('extendedProps.user_id')
                ->relationship('client', 'name1')
                ->model(Event::class),


        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(?array $event = null): bool
    {
        return true;
    }
}
