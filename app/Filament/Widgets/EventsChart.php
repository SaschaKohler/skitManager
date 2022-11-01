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



    public ?string $filter = '';


    protected function getFilters(): ?array

    {
        return
            User::query()->whereHas('events')->where('role_id' , '=',2 )->get()
            ->pluck('name1','id')->toArray();
    }

    protected function getData(): array
    {

        $users = User::whereHas('events', function ($query) {
            $query->where('start', '>=', Carbon::now()->startOfYear()->toDateString());
        })->withSum('events', 'event_user.sum')->get();


        $project = array();

        foreach ($users as $user) {
            $data = array();
            foreach ($user->events as $event) {
                $date = explode(' ', $event->start);
                $hours = number_format($event->pivot->sum / 3200, '2', '.', '.');

                $data[] = $date[0] . '::' . $event->title . '::'
                    . $hours;
            }
            $project[$user->id] = array('events' => $data);
        }

        $user = User::find($this->filter);

        $labels = array();
        $data = array();
        $backgroundColor = array();
        $borderColor = array();

            foreach ($project[$user->id] as $item) {
                foreach ($item as $dat) {

                    $values = explode('::', $dat);
                    array_push($labels, $values[0]);
                    array_push($data, $values[2]);
                    array_push($borderColor, $user->color);
                    $int_value = preg_split('/[(|)]/', $user->color);    // rgb(a,b,c)  -> rgba(a,b,c,o)
                    array_push($backgroundColor, 'rgba(' . $int_value[1] . ',0.2)');
                }
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
                    'label' => $user->name1 . ' ' . array_sum($data) . 'h',
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

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }
}
