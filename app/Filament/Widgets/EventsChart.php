<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EventsChart extends LineChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        //

        $users = User::query()
            ->select('users.*')
            ->join('event_user', 'event_user.user_id', '=', 'users.id')
            ->groupBy('users.id')
            ->count();

        return [];
//        $data = Trend::model(Event::class)
//            ->between(
//                start: now()->startOfYear(),
//                end: now()->endOfYear(),
//            )
//            ->dateColumn('start')
//            ->perMonth()
//            ->count();
//
//
//        return [
//            'datasets' => [
//                [
//                    'label' => 'Events per month',
//                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
//                ],
//            ],
//            'labels' => $data->map(fn(TrendValue $value) => $value->date),
//        ];
    }
}
