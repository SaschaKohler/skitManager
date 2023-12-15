<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Model;
>>>>>>> origin/master

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

<<<<<<< HEAD
=======

>>>>>>> origin/master
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
<<<<<<< HEAD
=======

    protected function afterSave()
    {
        $sum_items = round(collect($this->record->items)->map(function ($item) {
            return [
                'price' => $item['qty'] * $item['unit_price'] - $item['qty'] * $item['unit_price'] * $item['discount'] / 100
            ];
        })->sum('price'),2);

        if ($this->record->discount)
            $this->record->total_price = round($sum_items - $sum_items * $this->record->discount / 100,2);
        else
            $this->record->total_price = $sum_items;

        $this->record->save();
    }

>>>>>>> origin/master
}
