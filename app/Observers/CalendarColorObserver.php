<?php

namespace App\Observers;

use App\Models\Calendar;
use App\Models\Event;

class CalendarColorObserver
{
    /**
     * Handle the Calendar "created" event.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return void
     */
    public function created(Calendar $calendar)
    {
        //
    }

    /**
     * Handle the Calendar "updated" event.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return void
     */
    public function updated(Calendar $calendar)
    {
        if ($calendar->isDirty('color'))
        {
            $events = Event::query()->where('calendar_id','=', $calendar->id )->get();
            foreach ($events as $event)
            {
                $event->backgroundColor = $calendar->color;
                $event->borderColor = $calendar->color;

                $event->update();

            }
        }
    }

    /**
     * Handle the Calendar "deleted" event.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return void
     */
    public function deleted(Calendar $calendar)
    {
        //
    }

    /**
     * Handle the Calendar "restored" event.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return void
     */
    public function restored(Calendar $calendar)
    {
        //
    }

    /**
     * Handle the Calendar "force deleted" event.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return void
     */
    public function forceDeleted(Calendar $calendar)
    {
        //
    }
}
