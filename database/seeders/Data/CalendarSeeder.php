<?php

namespace Database\Seeders\Data;

use App\Actions\Clients\Calendar\CreateCalendarEvent;
use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEventType;
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
        $options = ['color' => 'yellow', 'textColor' => 'black'];

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Creating Calendar Events for ' . $client->name);

                $typesOfEvents = CalendarEventType::whereClientId($client->id)->get();

                foreach ($typesOfEvents as $eventType) {
                    for ($i = 1; $i <= 5; $i++) {
                        $datebetween = abs(($dateend - $datestart) / $daystep);
                        $randomday = rand(0, $datebetween);
                        $hour1 = rand(1, 12);
                        $hour2 = $hour1 + rand(1, 2);
                        $title = 'Test #' . $i . ' for ' . $client->name;
                        $start = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                        $end = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour2 . ':00:00';

                        $payload = [
                            'client_id' => $client->id,
                            'title' => $title,
                            'start' => $start,
                            'end' => $end,
                            'full_day_event' => 0,//todo:randomize,
                            'event_type_id' => $eventType->id
                        ];

                        CreateCalendarEvent::run($payload);

                    }
                }
            }
        }
    }
}
