<?php

namespace Database\Seeders\Data;

use App\Actions\Clients\Calendar\CreateCalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
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
        $amountOfEvents = 5;
        if (env('QUICK_SEED')) {
            $amountOfEvents = 2;
        }
        // Get all the Clients
        VarDumper::dump('Getting Clients');
        $clients = Client::whereActive(1)->get();

        /** Modify the below date range to change when the calendar events will populate for testing since time doesn't stand still. */
        $datestart = strtotime('2022-06-01');
        $dateend = strtotime('2022-07-31');
        $daystep = 86400;

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Creating Calendar Events for ' . $client->name);

                $typesOfEvents = CalendarEventType::whereClientId($client->id)->get();

                $randomUsers = [];
                foreach ($client->users as $user) {
                    $randomUsers[] = $user->id;
                }

                $locations = Location::whereClientId($client->id)->get();
                $locations = $locations->toArray();

                $randomUsers = array_values(array_unique($randomUsers));

                foreach ($typesOfEvents as $eventType) {
                    for ($i = 1; $i <= $amountOfEvents; $i++) {
                        $datebetween = abs(($dateend - $datestart) / $daystep);
                        $randomday = rand(0, $datebetween);
                        $hour1 = rand(3, 12);
                        $hour2 = $hour1 + rand(1, 2);
                        $title = 'Test #' . $i . ' for ' . $client->name;
                        $start = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                        $end = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour2 . ':00:00';

                        $attendees = [];
                        for ($d = 1; $d <= 2; $d++) {
                            $attendees[] = $randomUsers[rand(0, count($randomUsers) - 1)];
                        }

                        if (count($locations) > 0) {
                            $loc_id = $locations[rand(0, count($locations) - 1)]['id'];
                        } else {
                            $loc_id = null;
                        }


                        /* no leads bc leads seeder is in different project
                        $leadAttendees = [];
                        for ($d = 1; $d <= 10; $d++) {
                            $leadAttendees[] = rand(1,50);
                        }*/
                        $payload = [
                            'client_id' => $client->id,
                            'title' => $title,
                            'start' => $start,
                            'end' => $end,
                            'color' => $eventType->color,
                            'full_day_event' => 0,//todo:randomize,
                            'event_type_id' => $eventType->id,
                            'user_attendees' => $attendees,
                            'location_id' => $loc_id,
                            //'lead_attendees' => $leadAttendees
                        ];

                        CreateCalendarEvent::run($payload);
                    }
                }
            }
        }
    }
}
