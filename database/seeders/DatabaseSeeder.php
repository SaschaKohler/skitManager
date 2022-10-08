<?php

namespace Database\Seeders;

use App\Filament\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\Info;
use App\Models\Todo;
use App\Models\User;
use App\Models\Vehicle;
use Database\Factories\InfoFactory;
use Database\Factories\VehicleFactory;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        Storage::deleteDirectory('public');

//       \App\Models\Info::factory(10)->create();


        \App\Models\User::factory(9)
//            ->has(Info::factory())
            ->create();


        $user =User::factory()->create([
            'search' => 'DEMO USER',
            'phone1' => '12-345-67',
            'name1' => 'Demo User',
            'email' => 'admin@filamentphp.com',
            'role_id' => 1,
            'password' => Hash::make('password')
        ]);
        Calendar::factory()->create([
            'type' => 'Böschungsmähen',
            'description' => 'mit traktor Böschungen mähen',
            'color' => '#ffee77'
        ]);
        Calendar::factory()->create([
            'type' => 'Baumpflege',
            'description' => 'klettern ohne Ende',
            'color' => '#ff7765'
        ]);
        Calendar::factory()->create([
            'type' => 'pers. Termin',
            'description' => 'ab zum Kunden',
            'color' => '#445567'
        ]) ;
        $calendars = Calendar::all();
        $clients = User::query()->where('role_id','=',3)->get();
        $employees = User::query()->where('role_id','=',2)->get();


      //  \App\Models\Event::factory(10)->create();
        $events = \App\Models\Event::factory(15)->create()->each(function (Event $event) use ($clients,$employees,$calendars) {
            $event->client()->associate($clients->random());
            $event->calendar()->associate($calendars->random());
            $event->backgroundColor = $event->calendar->color;
            $event->borderColor = $event->calendar->color;
            $event->recurrence = 10;
            $event->save();
           // $event->employees()->sync($employees->random(random_int(1,3)));
    });

        Vehicle::factory(16)->create()->each(function (Vehicle $vehicle) use ($events){
            $vehicle->events()->sync($events->random(random_int(1,2)));
        });

        Todo::factory(20)->make()->each(function (Todo $todo) use ($employees) {
            $todo->assignee()->associate($employees->random());

            $todo->save();
        });


    }
}
