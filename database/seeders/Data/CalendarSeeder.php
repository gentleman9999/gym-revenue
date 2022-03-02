<?php

namespace Database\Seeders\Data;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\Clients\Client;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all the Clients
        VarDumper::dump('Getting Clients');
        $clients = Client::whereActive(1)->get();

        /** Modify the below date range to change when the calendar events will populate for testing since time doesn't stand still. */
        $datestart = strtotime('2022-03-01');
        $dateend = strtotime('2022-03-31');
        $daystep = 86400;

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Creating Events for '.$client->name);
                for($i = 1; $i <= 10; $i++){
                    $datebetween = abs(($dateend - $datestart) / $daystep);
                    $randomday = rand(0, $datebetween);

                    $aggy = CalendarAggregate::retrieve($client->id)->createCalendarEvent( 'Test #'.$i.' for '.$client->name,
                        date("Y-m-d", $datestart + ($randomday * $daystep)). ' 10:00:00',
                        date("Y-m-d", $datestart + ($randomday * $daystep)) .' 11:00:00')->persist();
                }
            }
        }
    }
}
