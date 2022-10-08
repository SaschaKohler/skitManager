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

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        // ...
        return Event::with(['client','employees'])
            ->where('start','>=',
                Carbon::today()->toDateString())->take(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('start')->date('D, d.M.y'),
            Tables\Columns\TextColumn::make('client.search'),
            Tables\Columns\TextColumn::make('employees.name1'),
            Tables\Columns\TextColumn::make('calendar.type')
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
