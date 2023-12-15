<?php

namespace App\Providers;

use App\Filament\Resources\CalendarResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VehicleResource;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Vite;
use Illuminate\Validation\ValidationException;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        //        Filament::registerRenderHook(
        //            'head.end',
        //            fn (): string => Blade::render('@vite([\'resources/css/filament.css\',\'resources/js/app.js\'])'),
        //        );

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::serving(
            function () {
                // Using Vite
                Filament::registerTheme(
                    app(Vite::class)('resources/css/filament.css'),
                );
                if(auth()->user()) {
                    if(auth()->user()->isAdmin()) {

                        Filament::registerUserMenuItems(
                            [
                            UserMenuItem::make()
                                ->label(__('filament::layout.buttons.manage_users.label'))
                                ->url(UserResource::getUrl())
                                ->icon('heroicon-s-users'),

                            UserMenuItem::make()
                                ->label(__('filament::layout.buttons.manage_calendars.label'))
                                ->url(CalendarResource::getUrl())
                                ->icon('heroicon-s-calendar'),

                            UserMenuItem::make()
                                ->label(__('filament::layout.buttons.manage_vehicles.label'))
                                ->url(VehicleResource::getUrl())
                                ->icon('heroicon-s-truck')

                            ]
                        );


                    }

                }

            }
        );


        //        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
        //            Notification::make()
        //                ->title($exception->getMessage())
        //                ->danger()
        //                ->send();
        //        };
    }
}
