<?php

namespace Database\Seeders;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\Todo;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
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


//        \App\Models\User::factory(5)
////            ->has(Info::factory())
//            ->create();

        Calendar::factory()->create([
            'type' => 'Zaunmontage'
        ]);
        Calendar::factory()->create([
            'type' => 'Gartenpflege'
        ]);
        Calendar::factory()->create([
            'type' => 'Baumpflege'
        ]);
        Calendar::factory()->create([
            'type' => 'Rasen anlegen'
        ]);
        Calendar::factory()->create([
            'type' => 'Transport'
        ]);
        Calendar::factory()->create([
            'type' => 'Winterdienst'
        ]);
        Calendar::factory()->create([
            'type' => 'Böschungsmähen'
        ]);
        Calendar::factory()->create([
            'type' => 'keine Kategorie'
        ]);




        User::factory()->create([
            'phone1' => '0650 903 3 72',
            'name1' => 'Sascha Kohler',
            'email' => 'admin@skit.at',
            'color' => 'rgb(54, 162, 235)',
            'role_id' => 1,
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'phone1' => '0664 2819670',
            'name1' => 'Karl Dirneder',
            'email' => 'dirneder@skit.at',
            'role_id' => 1,
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'phone1' => '0664 2819670',
            'name1' => 'Renate Bauernfeind',
            'email' => 'bauernfeind@skit.at',
            'role_id' => 1,
            'password' => Hash::make('password')
        ]);

        //     Calendar::factory(15)->create();

//        $calendars = Calendar::all();
//        $clients = User::query()->where('role_id', '=', 3)->get();
//        $employees = User::query()->where('role_id', '=', 2)->get();


//        $events = Event::factory()->count(20)
//            ->sequence(fn($sequence) => [
//                'user_id' => $clients->random(1)->first()->id,
//                'calendar_id' => $calendars->random(1)->first()->id
//                ])
//            ->employees()->sync($employees->random(rand(1,4)))
//            ->hasAttached($employees->random(3),
//
//                [
//                    'start_at' => Carbon::parse('07:00')->format('H:i'),
//                    'end_at' => Carbon::createFromFormat('H:i', '07:00')
//                        ->addMinutes(720)
//                        ->toDate()
//                        ->format('H:i'),
//                    'sum' => Carbon::parse()
//                ],'employees')
//
//                $employees->random(rand(1,3)),
//                [
//                    'start_at' => Carbon::parse('07:00')->format('H:i'),
//                    'end_at' => Carbon::createFromFormat('H:i', '07:00')
//                        ->addMinutes(rand(180, 690))
//                        ->toDate()
//                        ->format('H:i')
//                ],'employees'
//            )
//            ->create();
        //  \App\Models\Event::factory(10)->create();
//        $events = Event::factory(15)->create()->each(function (Event $event) use ($clients, $employees, $calendars) {
//            $event->client()->associate($clients->random());
//            $event->calendar()->associate($calendars->random());
//            $event->backgroundColor = $event->calendar->color;
//            $event->borderColor = $event->calendar->color;
//            $event->recurrence = 10;
//            $event->save();
//            $event->employees()->sync($employees->random(rand(2,4))
//
//            );
//    });

        Vehicle::factory(16)->create();//->each(function (Vehicle $vehicle) use ($events) {
     //       $vehicle->events()->sync($events->random(random_int(1, 2)));
       // });

//        Todo::factory(20)->make()->each(function (Todo $todo) use ($employees) {
//            $todo->assignee()->associate($employees->random());
//
//            $todo->save();
//        });


    }
}
