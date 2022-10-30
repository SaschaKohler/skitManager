<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\User;
use Barryvdh\Debugbar\Controllers\AssetController;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EventsChart extends BarChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?string $pollingInterval = null;


    public ?string $filter = 'yesterday';


    protected function getFilters(): ?array

    {


        return [

            'yesterday' => 'Yesterday',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        if ($activeFilter == 'yesterday')
            $users = User::whereHas('events', function ($query) {
                $query->where('start', '>=', Carbon::now()->subDays(1)
                    ->toDate()->format('Y-m-d'));
            })->with('events')->get();
        elseif ($activeFilter == 'week')
            $users = User::whereHas('events', function ($query) {
                $query->where('start', '>=', Carbon::now()->subWeeks(1)
                    ->toDate()->format('Y-m-d'));
            })->with('events')->get();
        elseif ($activeFilter == 'month')
            $users = User::whereHas('events', function ($query) {
                $query->where('start', '>=', Carbon::now()->subWeeks(4)
                    ->toDate()->format('Y-m-d'));
            })->with('events')->get();
        elseif ($activeFilter == 'year')
            $users = User::whereHas('events', function ($query) {
                $query->where('start', '>=', Carbon::now()->startOfYear()
                    ->toDate()->format('Y-m-d'));
            })->with('events')->get();


        $labels = array();
        $data = array();
        $backgroundColor = array();
        $borderColor = array();
        foreach ($users as $user) {
            array_push($labels, $user->name1);
            array_push($data, $user->events->sum('pivot.sum') / 3600);   // seconds -> hours
            array_push($borderColor, $user->color);
            $int_value = preg_split('/[(|)]/', $user->color);    // rgb(a,b,c)  -> rgba(a,b,c,o)
            array_push($backgroundColor, 'rgba(' . $int_value[1] . ',0.2)');
        }

//        $data = Trend::query(User::whereHas('events')->with('events'))
//            ->between(
//                start: now()->startOfYear(),
//                end: now()->endOfYear(),
//            )
//            ->perYear();


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
        return [
            'datasets' => [
                [
                    'label' => 'Hours in sum (' . $activeFilter . ')',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 1,
                    'hoverBorderWidth' => 2


                ],
            ],
            'labels' => $labels
        ];
    }
}
