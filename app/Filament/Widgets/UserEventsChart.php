<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\BarChartWidget;

class UserEventsChart extends BarChartWidget
{
    protected static ?string $heading = 'Zeiten';
    protected static ?string $pollingInterval = '10s';

//    public ?string $filter = 'none';


    /**
     * @param string|null $filter
     */

//    protected function getFilters(): ?array
//
//    {
//        return
//            User::query()->whereHas('events')->whereIn('role_id', [1,2])->get()
//                ->pluck('name1', 'id')->toArray();
//    }

    protected function getData(): array
    {


        $user = User::find(auth()->user()->id);

        User::
            $project = array();

                $data = array();
                foreach ($user->events as $event) {
                    $date = explode(' ', $event->start);
                    $hours = number_format($event->pivot->sum / 3600, '2', '.', '.');

                    $data[] = $date[0] . '::' . $event->title . '::'
                        . $hours;
                }
                $project[$user->id] = array('events' => $data);


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
        return auth()->user()->role_id == 2;
    }

}
