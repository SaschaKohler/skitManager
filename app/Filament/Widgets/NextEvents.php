<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class NextEvents extends BaseWidget
{
    protected static ?string $heading = 'nächste Baustellen';
    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        // ...
        return Event::with(['client','employees'])
            ->where('start','>=',
                Carbon::today()->toDateString())->take(6);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('start')
                ->label(__('filament::resources/event-resource.table.start'))
                ->date('D, d.M.y'),
            Tables\Columns\TextColumn::make('client.name1')
                ->label(__('filament::resources/event-resource.table.client')),
            Tables\Columns\TextColumn::make('employees.name1')
                ->label(__('filament::resources/event-resource.table.employees')),
            Tables\Columns\TextColumn::make('calendar.type')
                ->label(__('filament::resources/event-resource.table.calendar_type'))

        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }


}
