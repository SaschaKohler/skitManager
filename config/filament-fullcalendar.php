<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

    'locale' => config('app.locale'),
    'initialView' => 'timeGridWeek',

    'headerToolbar' => [
        'left' => 'prev,next today',
        'center' => 'title',
        'right' => 'timeGridDay,timeGridWeek,list14Day,dayGridMonth',
    ],
    'views' => [
        'list14Day' => [
            'type' => 'list',
            'duration' => ['days' => '14'],
            'buttonText' => 'nächsten 14 Tage'
        ],
    ],

    'navLinks' => true,
    'slotMinTime' => '04:00:00',
    'slotMaxTime' => '23:00:00',
    'slotDuration' => '00:30',


    'editable' => true,

    'selectable' => true,

    'dayMaxEvents' => true,
];
