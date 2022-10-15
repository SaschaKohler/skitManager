<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

//    'plugins' => ['google-calendar'],
    'googleCalendarApiKey' => 'AIzaSyB1XI1v4UR56-YAuaf4hnTwJgum6_hAUZU',
    'events' => [
        'googleCalendarId' => 'sascha.cloud.01@gmail.com',
    ],
    'locale' => config('app.locale'),
    'headerToolbar' => [
        'left' => 'prev,next today',
        'center' => 'title',
        'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
    ],

    'navLinks' => true,
    'slotMinTime' => '06:00:00',
    'slotMaxTime' => '22:00:00',

    'editable' => true,

    'selectable' => false,

    'dayMaxEvents' => true,
];
