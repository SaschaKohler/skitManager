<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\CreateAction::make(),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterSave(): void
    {
        $event = $this->record;

        $event->backgroundColor = $event->calendar()->pluck('color')[0];
        $event->borderColor = $event->calendar()->pluck('color')[0];

        $event->update();

        if ($event->employees->count()) {

            foreach ($event->employees as $employee) {

                Notification::make()
                    ->title('Eintrag geändert')
                    ->icon('heroicon-s-calendar')
                    ->body("**{$event->title}** / **{$event->calendar->type}**\\
            Kunde: *{$event->client->name1}* am *{$event->start}*")
                    ->actions([
                        Action::make('View')
                            ->url(EventResource::getUrl('edit', ['record' => $event])),
                    ])
                    ->sendToDatabase($employee);
            }

        }

    }

}
