<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

<<<<<<< HEAD
<<<<<<< HEAD
   // 'plugins' => ['timeGridPlugin'],
=======
   // 'plugins' => ['dayGridView'],
    'googleCalendarApiKey' => 'AIzaSyB1XI1v4UR56-YAuaf4hnTwJgum6_hAUZU',
    'events' => [
        'googleCalendarId' => 'sascha.cloud.01@gmail.com',
    ],
>>>>>>> cdec0f1 (statistics translations)
=======
>>>>>>> origin/master
    'locale' => config('app.locale'),
    'initialView' => 'timeGridWeek',

    'headerToolbar' => [
        'left' => 'prev,next today',
        'center' => 'title',
<<<<<<< HEAD
        'right' => 'listSevenDay,gridSevenDay,timeGridWeek,dayGridMonth',
    ],
    'views' => [
        'listSevenDay' => [
            'type' => 'list',
            'duration' => [ 'days' => '14' ],
            'buttonText' => 'nächsten 14 Tage'
        ],
        'gridSevenDay' => [
            'type' => 'timeGrid',
            'duration' => [ 'days' => 7 ],
            'slotDuration' => '00:10',
            'slotMinTime' => '06:00',
            'slotMaxTime' => '22:00',
            'buttonText' => 'nächsten 7 Tage',
            'slotLabelInterval' => '01:00',
            'slotLabelFormat' => [
                'hour' => 'numeric',
                'minute' => '2-digit',
                'omitZeroMinute' => false,
                'meridiem' => 'short'
            ]
        ]
    ],

    'navLinks' => true,
    'slotMinTime' => '06:00:00',
    'slotMaxTime' => '22:00:00',
    'slotDuration' => '00:20',
=======
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
>>>>>>> origin/master


    'editable' => true,

    'selectable' => true,

    'dayMaxEvents' => true,
];
