<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Event;
<<<<<<< HEAD
use Closure;
use DateInterval;
use DateTime;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
=======
use App\Models\User;
use Carbon\Carbon;
use DatInterval;
use DateTime;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
>>>>>>> origin/master
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

=======
use PHPUnit\Exception;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

use function PHPUnit\Framework\isInstanceOf;

>>>>>>> origin/master
class CalendarWidget extends FullCalendarWidget
{
    protected static string $view = 'filament.resources.event-resource.widgets.calendar-widget';

<<<<<<< HEAD
=======
    protected string $modalWidth = 'lg';
    protected static ?string $title = 'fun';

    protected static ?string $heading = 'Total customers';

>>>>>>> origin/master

    public function fetchEvents(array $fetchInfo): array
    {
        $user = auth()->user();

        if ($user->role_id == 2) {

            // You can use $fetchInfo to filter events by date.

<<<<<<< HEAD
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

=======
            $employeeEvents = Event::query()
                ->where(
                    [
                    ['start', '>=', $fetchInfo['start']],
                    ['end', '<=', $fetchInfo['end']],
                    ]
                )
                ->whereHas(
                    'employees', function (Builder $query) {
                        $query->where('user_id', auth()->id());
                    }
                )
                ->get()->toArray();

            return $employeeEvents;
        }


        $myEvents = Event::query()
            ->where(
                [
                ['start', '>=', $fetchInfo['start']],
                ['end', '<=', $fetchInfo['end']],
                ]
            )
            ->get()->flatten()->toArray();


        if ($gcal = \Spatie\GoogleCalendar\Event::get(startDateTime: Carbon::now()->subDays(14))) {


            $google_events = $gcal->map(
                function ($events) {
                    $color_id = $events->colorId ? $events->colorId : 'undefined';
                    $calendar = Calendar::select('id', 'color')
                        ->where('color_id', '=', $color_id)->get();
                    return [
                    'id' => $events->id,
                    'title' => $events->summary . ' **GOOGLE-CALENDAR**',
                    'start' => Carbon::parse($events->startDateTime)->toDateTimeString(),
                    'end' => Carbon::parse($events->endDateTime)->toDateTimeString(),
                    'backgroundColor' => $calendar[0]->color,
                    'borderColor' => $calendar[0]->color,
                    'calendar_id' => $calendar[0]->id,
                    ];
                }
            )->toArray();

            $google_events_coll = collect($google_events);


            $items = array();
            foreach ($myEvents as $event) {
                array_push($items, $event['google_id']);
            }

            $filter = $google_events_coll->whereNotIn('id', $items)->toArray();
            return array_merge($filter, $myEvents);

        }

        return $myEvents;


    }


>>>>>>> origin/master
    protected function getFormModel(): Model|string|null
    {
        return $this->event ?? Event::class;
    }

<<<<<<< HEAD
    public function url($event)
    {
        $event = Event::find($event['id']);
        $url = EventResource::getUrl('edit', ['record' => $event->id]);

        $this->redirect($url);

=======
    public function url($param)
    {
        if (is_numeric($param['id'])) {            // google_id is none numeric  local events have primary key numeric
            $event = Event::find($param['id'])->toArray();
            $url = EventResource::getUrl('edit', ['record' => $event['id']]);

        } else {
            $user = User::where('name1', 'like', '%' . explode(' ', $param['title'])[0] . '%')->first();
            $new = new Event();
            $new->google_id = $param['id'];
            $new->title = explode(' *', $param['title'])[0];
            $new->start = $param['start'];
            $new->end = $param['end'];
            $new->author_id = auth()->id();
            $new->calendar_id = $param['extendedProps']['calendar_id'];

            if ($user) {
                $new->user_id = $user['id'];
            } else {
                $new->user_id = User::where('name1', '=', 'KUNDE')->first()['id'];
            }

            $new->save();

            $url = EventResource::getUrl('edit', ['record' => $new->id]);
        }

        $this->redirect($url);


>>>>>>> origin/master
    }

    public function onEventClick($event): void
    {
<<<<<<< HEAD
       // parent::onEventClick($event);


      //  return fn (Model $record): string => route('posts.edit', ['record' => $record]);


        // your code
        //   $this->editEventForm->model($this->event);
<<<<<<< HEAD:app/Filament/Resources/EventResource/Widgets/CalendarWidget.php
        $event = Event::find($event['id']);

=======
        //  return $event;
      $this->url($event);
>>>>>>> 42bf679 (updates updates!):app/Filament/Widgets/CalendarWidget.php
=======
        // parent::onEventClick($event);

        $this->url($event);
>>>>>>> origin/master

    }

    public function onEventDrop($newEvent, $oldEvent, $relatedEvents): void
    {
<<<<<<< HEAD

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

=======
        if (array_key_exists('google_id', $oldEvent['extendedProps'])) {

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

            Notification::make()
                ->title('Eintrag geändert')
                ->icon('heroicon-o-document-text')
                ->iconColor('success')
                ->duration(5000)
                ->send();

            if ($this->event->employees->count()) {

                $this->sendNotificationsToEmployees($newEvent);

            }
            $this->refreshEvents();
            return;
        }

        $this->refreshEvents();

        Notification::make()
            ->title('nicht möglich -> google-calendar')
            ->icon('heroicon-o-shield-exclamation')
            ->iconColor('danger')
            ->duration(5000)
            ->send();
>>>>>>> origin/master
    }

    /**
     * Triggered when event's resize stops.
     */
    public function onEventResize($event, $oldEvent, $relatedEvents): void
    {
        // your code
<<<<<<< HEAD
        $this->event = $this->resolveEventRecord($event);
        $this->event->update($event);
        $this->refreshEvents();

=======
        if (array_key_exists('google_id', $oldEvent['extendedProps'])) {

            $this->event = $this->resolveEventRecord($event);
            $this->event->update($event);

            Notification::make()
                ->title('Eintrag geändert')
                ->icon('heroicon-o-document-text')
                ->iconColor('success')
                ->duration(5000)
                ->send();

            if ($this->event->employees->count()) {
                $this->sendNotificationsToEmployees($event);

                $this->refreshEvents();
                return;

            }
            $this->refreshEvents();
            Notification::make()
                ->title('nicht möglich -> google-calendar')
                ->icon('heroicon-o-shield-exclamation')
                ->iconColor('danger')
                ->duration(5000)
                ->send();


        }
>>>>>>> origin/master
    }

    public function createEvent(array $event): void
    {
<<<<<<< HEAD
=======

        $calendar = Calendar::find($event['extendedProps']['calendar_id']);


>>>>>>> origin/master
        // Create the event with the provided $data.
        $data = $event;
        $data['user_id'] = $event['extendedProps']['user_id'];
        $data['allDay'] = $event['extendedProps']['allDay'];
        $data['calendar_id'] = $event['extendedProps']['calendar_id'];
<<<<<<< HEAD
       // $data['backgroundColor'] = $event['extendedProps']['backgroundColor'];
       // $data['borderColor'] = $event['extendedProps']['backgroundColor'];
=======
        $data['backgroundColor'] = $calendar->color;
        $data['borderColor'] = $calendar->color;
>>>>>>> origin/master
        $data['recurrence'] = $data['extendedProps']['recurrence'];


        $this->event = Event::create($data);
<<<<<<< HEAD
        $this->event->backgroundColor = $this->event->calendar->color;
        $this->event->borderColor = $this->event->calendar->color;
        $this->event->update();
=======


        //        $this->event->backgroundColor = $this->event->calendar()->pluck('backgroundColor')[0];
        //        $this->event->borderColor = $this->event->calendar()->pluck('borderColor')[0];
        //        $this->event->textColor = $this->event->calendar()->pluck('textColor')[0];
        //        $this->event->update();
>>>>>>> origin/master
        $this->refreshEvents();

        Notification::make()
            ->title('Neuer Eintrag')
            //->icon('heroicon-s-calender')
            ->body("**{$this->event->title} am {$this->event->start}**")
<<<<<<< HEAD
            ->actions([
                Action::make('View')
                    ->url(EventResource::getUrl('edit', ['record' => $this->event])),
            ])
=======
            ->actions(
                [
                Action::make('View')
                    ->url(EventResource::getUrl('edit', ['record' => $this->event])),
                ]
            )
>>>>>>> origin/master
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

<<<<<<< HEAD
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
=======
        // $this->event_id
        // the value is retrieved from event's id key
        // eg: Appointment::find($this->event);

        // $this->event
        // model instance is resolved by user defined resolveEventRecord() funtion. See example below
        $dat = $data;
        $dat['allDay'] = $data['extendedProps']['allDay'];
        $dat['calendar_id'] = (int)$data['extendedProps']['calendar_id'];
        $dat['recurrence'] = $data['extendedProps']['recurrence'];

        //  $this->event->
        $this->event->update($dat);
        $this->event->backgroundColor = $this->event->calendar()->pluck('backgroundColor')[0];
        $this->event->borderColor = $this->event->calendar()->pluck('borderColor')[0];
        $this->event->textColor = $this->event->calendar()->pluck('textColor')[0];
>>>>>>> origin/master
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
=======
            Forms\Components\Grid::make()
                ->schema(
                    [
>>>>>>> origin/master
                    Forms\Components\Toggle::make('extendedProps.allDay')
                        ->label(__('filament::widgets/calendar-widget.allday'))
                        ->columnSpan(2),
                    Forms\Components\Select::make('extendedProps.calendar_id')
                        ->disableLabel()
<<<<<<< HEAD
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
=======
                        ->options(
                            function () {
                                $calendars = Calendar::all();
                                return $calendars->mapWithKeys(
                                    function ($calendars) {
                                        return [$calendars->getKey() => static::getCleanOptionString($calendars)];
                                    }
                                )->toArray();
                            }
                        )
                        ->required()
                        ->allowHtml()
                        ->searchable()
                        ->getSearchResultsUsing(
                            function (string $query) {
                                $calendar = Calendar::where('type', 'like', "%{$query}%")
                                    ->limit(50)
                                    ->get();
                                return $calendar->mapWithKeys(
                                    function ($calendar) {
                                        return [$calendar->getKey() => static::getCleanOptionString($calendar)];
                                    }
                                )->toArray();
                            }
                        )
                        ->getOptionLabelUsing(
                            function ($value): string {
                                $calendar = Calendar::find($value);
                                return static::getCleanOptionString($calendar);
                            }
                        )
                        ->columnSpan(2),

                    ]
                )->columns(4),

            Forms\Components\Card::make()
                ->schema(
                    [
>>>>>>> origin/master
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

<<<<<<< HEAD
                ])->columns('4'),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Select::make('extendedProps.recurrence')
                        ->label(__('filament::widgets/calendar-widget.recurrence.header'))
                        ->options([
                            '10' =>__('filament::widgets/calendar-widget.recurrence.none'),
=======
                    ]
                )->columns('4'),

            Forms\Components\Card::make()
                ->schema(
                    [
                    Forms\Components\Select::make('extendedProps.recurrence')
                        ->label(__('filament::widgets/calendar-widget.recurrence.header'))
                        ->options(
                            [
                            '10' => __('filament::widgets/calendar-widget.recurrence.none'),
>>>>>>> origin/master
                            '1' => __('filament::widgets/calendar-widget.recurrence.daily'),
                            '2' => __('filament::widgets/calendar-widget.recurrence.weekly'),
                            '3' => __('filament::widgets/calendar-widget.recurrence.14day'),
                            '4' => __('filament::widgets/calendar-widget.recurrence.3week'),
                            '5' => __('filament::widgets/calendar-widget.recurrence.monthly'),
                            '6' => __('filament::widgets/calendar-widget.recurrence.3month'),
                            '7' => __('filament::widgets/calendar-widget.recurrence.halfyear'),
                            '8' => __('filament::widgets/calendar-widget.recurrence.yearly'),
<<<<<<< HEAD
                        ])
=======
                            ]
                        )
>>>>>>> origin/master
                        ->required()
                        ->allowHtml(),

                    Forms\Components\Select::make('extendedProps.user_id')
                        ->label(__('filament::widgets/calendar-widget.user_id'))
                        ->required()
                        ->searchable()
                        ->preload()
<<<<<<< HEAD
                        ->options(function () {
                            return User::where('role_id', 3)->pluck('name1', 'id');
                        })
                        ->getSearchResultsUsing(function ($query){
                            return User::where('name1', 'like', "%{$query}%")->
                            where('role_id', 3)->pluck('name1', 'id');
                        })

                ])
=======
                        ->options(
                            function () {
                                return User::where('role_id', 3)->pluck('name1', 'id');
                            }
                        )
                        ->getSearchResultsUsing(
                            function ($query) {
                                return User::where('name1', 'like', "%{$query}%")->
                                where('role_id', 3)->pluck('name1', 'id');
                            }
                        )

                    ]
                )
>>>>>>> origin/master

        ];

    }

<<<<<<< HEAD
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
                        ->required()
                        ->allowHtml()
                        ->searchable()
                        ->options(function () {
                            $calendars = Calendar::all();
                            return $calendars->mapWithKeys(function ($calendars) {
                                return [$calendars->getKey() => static::getCleanOptionString($calendars)];
                            })->toArray();
                        })
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

=======
>>>>>>> origin/master
    protected static function getEditEventFormSchema(): array
    {

        return [
<<<<<<< HEAD
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

=======

            Forms\Components\Grid::make()
                ->schema(
                    [
                    Forms\Components\Toggle::make('extendedProps.allDay')
                        ->label(__('filament::widgets/calendar-widget.allday'))
                        ->columnSpan(2),
                    Forms\Components\Select::make('extendedProps.calendar_id')
                        ->disableLabel()
                        ->required()
                        ->allowHtml()
                        ->searchable()
                        ->options(
                            function () {
                                $calendars = Calendar::all();
                                return $calendars->mapWithKeys(
                                    function ($calendars) {
                                        return [$calendars->getKey() => static::getCleanOptionString($calendars)];
                                    }
                                )->toArray();
                            }
                        )
                        ->getSearchResultsUsing(
                            function (string $query) {
                                $calendar = Calendar::where('type', 'like', "%{$query}%")
                                    ->limit(50)
                                    ->get();
                                return $calendar->mapWithKeys(
                                    function ($calendar) {
                                        return [$calendar->getKey() => static::getCleanOptionString($calendar)];
                                    }
                                )->toArray();
                            }
                        )
                        ->getOptionLabelUsing(
                            function ($value): string {
                                $calendar = Calendar::find($value);
                                return static::getCleanOptionString($calendar);
                            }
                        )
                        ->columnSpan(2),

                    ]
                )->columns(4),

            Forms\Components\Card::make()
                ->schema(
                    [

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

                    ]
                )->columns('4'),

            Forms\Components\Card::make()
                ->schema(
                    [
                    Forms\Components\Select::make('extendedProps.recurrence')
                        ->label(__('filament::widgets/calendar-widget.recurrence.header'))
                        ->options(
                            [
                            '10' => __('filament::widgets/calendar-widget.recurrence.none'),
                            '1' => __('filament::widgets/calendar-widget.recurrence.daily'),
                            '2' => __('filament::widgets/calendar-widget.recurrence.weekly'),
                            '3' => __('filament::widgets/calendar-widget.recurrence.14day'),
                            '4' => __('filament::widgets/calendar-widget.recurrence.3week'),
                            '5' => __('filament::widgets/calendar-widget.recurrence.monthly'),
                            '6' => __('filament::widgets/calendar-widget.recurrence.3month'),
                            '7' => __('filament::widgets/calendar-widget.recurrence.halfyear'),
                            '8' => __('filament::widgets/calendar-widget.recurrence.yearly'),
                            ]
                        )
                        ->required()
                        ->allowHtml(),

                    Forms\Components\Select::make('extendedProps.user_id')
                        ->label(__('filament::widgets/calendar-widget.user_id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(
                            function () {
                                return User::where('role_id', 3)->pluck('name1', 'id');
                            }
                        )
                        ->getSearchResultsUsing(
                            function ($query) {
                                return User::where('name1', 'like', "%{$query}%")->
                                where('role_id', 3)->pluck('name1', 'id');
                            }
                        )

                    ]
                )
>>>>>>> origin/master

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
<<<<<<< HEAD
<<<<<<< HEAD:app/Filament/Resources/EventResource/Widgets/CalendarWidget.php
=======

    public
    static function getCleanOptionString(Model $model): string
    {
        return //Purify::clean(
            view('filament.components.select-calendar-result')
                ->with('type', $model?->type)
                ->with('description', $model?->description)
                ->with('color', $model?->color)
                ->render();
=======

    public static function getCleanOptionString(Model $model): string
    {
        return //Purify::clean(
            view('filament.components.select-calendar-result')
            ->with('type', $model?->type)
            ->with('description', $model?->description)
            ->with('color', $model?->color)
            ->render();
>>>>>>> origin/master

    }

    protected function getFormComponentActions(): array
    {
        return [
<<<<<<< HEAD
           \Filament\Tables\Actions\Action::make('action')
        ];
    }
>>>>>>> 42bf679 (updates updates!):app/Filament/Widgets/CalendarWidget.php
=======
            \Filament\Tables\Actions\Action::make('action')
        ];
    }


    private function sendNotificationsToEmployees(?array $event)
    {

        $this->event = $this->resolveEventRecord($event);

        foreach ($this->event->employees as $employee) {

            Notification::make()
                ->title('Eintrag geändert')
                ->icon('heroicon-o-shield-exclamation')
                ->iconColor('success')
                ->body(
                    "**{$this->event->title}** / **{$this->event->calendar->type}**\\
            Kunde: *{$this->event->client->name1}* am *{$this->event->start}*"
                )
                ->actions(
                    [
                    Action::make('View')
                        ->url(EventResource::getUrl('edit', ['record' => $this->event])),
                    ]
                )
                ->sendToDatabase($employee);
        }


    }
>>>>>>> origin/master
}
