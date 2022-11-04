<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

    'locale' => config('app.locale'),
    'initialView' => 'gridSevenDay',

    'headerToolbar' => [
        'left' => 'prev,next today',
        'center' => 'title',
        'right' => 'list14Day,gridSevenDay,dayGridMonth',
    ],
    'views' => [
        'list14Day' => [
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
    'slotDuration' => '00:10',


    'editable' => true,

    'selectable' => false,

    'dayMaxEvents' => true,
];
