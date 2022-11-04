<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use function PHPUnit\Framework\isInstanceOf;

class CalendarWidget extends FullCalendarWidget
{
    protected static string $view = 'filament.resources.event-resource.widgets.calendar-widget';

    protected string $modalWidth = 'lg';



    public function fetchEvents(array $fetchInfo): array
    {
        $user = auth()->user();

//        if ($user->role_id == 2) {
//
//            // You can use $fetchInfo to filter events by date.
//
//            $myEvents = Event::select('title','google_id','start','end',
//            'backgroundColor','borderColor')
//                ->where([
//                    ['start', '>=', $fetchInfo['start']],
//                    ['end', '<=', $fetchInfo['end']],
//                ])
//                ->whereHas('employees', function (Builder $query) {
//                    $query->where('user_id', auth()->id());
//                })
//                ->get();
//
//
//        }

        $myEvents = Event::query()
            ->where([
                ['start', '>=', $fetchInfo['start']],
                ['end', '<=', $fetchInfo['end']],
            ])
            ->get()->flatten()->toArray();
        $gcal = \Spatie\GoogleCalendar\Event::get();

        $google_events = $gcal->map(function ($events) {
            $color_id = $events->colorId ? $events->colorId : 'undefined';
            $calendar = Calendar::select('id','color')
                ->where('color_id','=',$color_id)->get()
                ->toArray();
            return [
                'id' => $events->id,
                'title' => $events->summary,
                'start' => Carbon::parse($events->startDateTime)->toDateTimeString(),
                'end' => Carbon::parse($events->endDateTime)->toDateTimeString(),
                'backgroundColor' => $calendar[0]['color'],
                'borderColor' => $calendar[0]['color'],
                'calendar_id' => $calendar[0]['id'],
                ];
        })->toArray();


//        dd(
//            array_diff(
//                array_map('serialize', $google_events),
//                array_map('serialize', $myEvents)
//            )
//        );
//dd($gcal,$google_events);

//        dd(collect($myEvents)->filter(function ($item) {
//            return $item;
//        }));

        $items = array();
        foreach($myEvents as $event){
            array_push($items,$event['google_id']);
        }

        $google_events_coll = collect($google_events);

        $filter = $google_events_coll->whereNotIn('id' , $items)->toArray();

        return array_merge($filter,$myEvents);

    }



    protected function getFormModel(): Model|string|null
    {
        return $this->event ?? Event::class;
    }

    public function url($param)
    {
        $event = Event::query()
            ->where('title', '=', $param['title'])
            ->get()->toArray();
        if ($event) {
            $url = EventResource::getUrl('edit', ['record' => $event[0]['id']]);

        } else {
            $user = User::where('name1', 'like', '%' . $param['title'] . '%')->first();
            $new = new Event();
            $new->google_id = $param['id'];
            $new->title = $param['title'];
            $new->start = $param['start'];
            $new->end = $param['end'];
            $new->user_id = 4;
            $new->calendar_id = $param['extendedProps']['calendar_id'];
            if ($user) $new->user_id = $user['id'];
            $new->save();


//            $to_del = \Spatie\GoogleCalendar\Event::find($param['id']);
//            $to_del->delete();


            $url = EventResource::getUrl('edit', ['record' => $new->id]);
        }

        $this->redirect($url);


    }

    public function onEventClick($event): void
    {
        // parent::onEventClick($event);


        //  return fn (Model $record): string => route('posts.edit', ['record' => $record]);

        // your code
        //   $this->editEventForm->model($this->event);
        //  return $event;
        $this->url($event);

    }

    public function onEventDrop($newEvent, $oldEvent, $relatedEvents): void
    {


        if(array_key_exists('extendedProps',$oldEvent)) {

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


        $this->event->backgroundColor = $this->event->calendar()->pluck('backgroundColor')[0];
        $this->event->borderColor = $this->event->calendar()->pluck('borderColor')[0];
        $this->event->textColor = $this->event->calendar()->pluck('textColor')[0];
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
        $dat['calendar_id'] = (int)$data['extendedProps']['calendar_id'];
        $dat['recurrence'] = $data['extendedProps']['recurrence'];

        //  $this->event->
        $this->event->update($dat);
        $this->event->backgroundColor = $this->event->calendar()->pluck('backgroundColor')[0];
        $this->event->borderColor = $this->event->calendar()->pluck('borderColor')[0];
        $this->event->textColor = $this->event->calendar()->pluck('textColor')[0];
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
                            '10' => __('filament::widgets/calendar-widget.recurrence.none'),
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
                            '10' => __('filament::widgets/calendar-widget.recurrence.none'),
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

                ])

        ];
    }

    public
    static function canCreate(): bool
    {
        return true;
    }

    public
    static function canEdit(?array $event = null): bool
    {
        return true;
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

    protected function getFormComponentActions(): array
    {
        return [
            \Filament\Tables\Actions\Action::make('action')
        ];
    }
}
