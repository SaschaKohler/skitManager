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
//            ->has(Info::factory())
//            ->create();

        //Color ID	Color Name	Hex Code
//undefined	Who knows	#039be5
//1	Lavender	#7986cb
//2	Sage	#33b679
//3	Grape	#8e24aa
//4	Flamingo	#e67c73
//5	Banana	#f6c026
//6	Tangerine	#f5511d
//7	Peacock	#039be5
//8	Graphite	#616161
//9	Blueberry	#3f51b5
//10Basil	#0b8043
//11Tomato	#d60000

        Calendar::factory()->create([
            'type' => 'ohne Kategorie',
            'color_id' => 'undefined',
            'description' => 'office@dirneder.at',
            'color' => '#039be5'
        ]);

        Calendar::factory()->create([
            'type' => 'Stockfräsen',
            'color_id' => '1',
            'description' => 'Lavender',
            'color' => '#7986cb'
        ]);
        Calendar::factory()->create([
            'type' => 'Gartenpflege',
            'color_id' => '2',
            'description' => 'Sage',
            'color' => '#33b679'
        ]);
        Calendar::factory()->create([
            'type' => 'Zaunmontage',
            'color_id' => '3',
            'description' => 'Grape',
            'color' => '#8e24aa'
        ]);
        Calendar::factory()->create([
            'type' => 'Rasen anlegen',
            'color_id' => '4',
            'description' => 'Flamingo',
            'color' => '#e67c73'
        ]);
        Calendar::factory()->create([
            'type' => 'Hecke schneiden',
            'color_id' => '5',
            'description' => 'Banana',
            'color' => '#f6c026'
        ]);
        Calendar::factory()->create([
            'type' => 'pers. Termin',
            'color_id' => '6',
            'description' => 'Tangerine',
            'color' => '#f5511d'
        ]);
        Calendar::factory()->create([
            'type' => 'Böschungsmähen',
            'color_id' => '7',
            'description' => 'Peacock',
            'color' => '#039be5'
        ]);
          Calendar::factory()->create([
              'type' => 'Böschungsmähen',
              'color_id' => '8',
              'description' => 'Graphite',
              'color' => '#616161'
          ]);
          Calendar::factory()->create([
              'type' => 'Angebot bestätigt',
              'color_id' => '9',
              'description' => 'Blueberry',
              'color' => '#3f51b5'
          ]);
          Calendar::factory()->create([
              'type' => 'Baumpflege',
              'color_id' => '10',
              'description' => 'Basil',
              'color' => '#0b8043'
          ]);
          Calendar::factory()->create([
              'type' => 'Lieferdatum',
              'color_id' => '11',
              'description' => 'Tomato',
              'color' => '#d60000'
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
  //  });
        Seeder::call(ZipCodesTableSeeder::class);

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
