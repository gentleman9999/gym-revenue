<?php

namespace Database\Seeders\Data;

use App\Actions\Clients\Calendar\CreateCalendarEvent;
use App\Models\CalendarEventType;
use App\Models\Clients\Client;
use App\Models\User;
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
        $dateend = strtotime('2022-05-31');
        $daystep = 86400;

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Creating Calendar Events for ' . $client->name);

                $typesOfEvents = CalendarEventType::whereClientId($client->id)->get();

                $users = User::whereHas('detail', function ($query) use ($client) {
                    return $query->whereName('associated_client')->whereValue($client->id);
                })->get();

                $randomUsers = [];
                foreach($users as $user)
                {
                    $randomUsers[] = $user->id;
                }

                $randomUsers = array_values(array_unique($randomUsers));

                foreach ($typesOfEvents as $eventType) {
                    for ($i = 1; $i <= 5; $i++) {
                        $datebetween = abs(($dateend - $datestart) / $daystep);
                        $randomday = rand(0, $datebetween);
                        $hour1 = rand(3, 12);
                        $hour2 = $hour1 + rand(1, 2);
                        $title = 'Test #' . $i . ' for ' . $client->name;
                        $start = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                        $end = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour2 . ':00:00';

                        $attendees = [];
                        for ($d = 1; $d <= 5; $d++) {
                            $attendees[] = $randomUsers[rand(0,count($randomUsers)-1)];
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
                            'attendees' => $attendees,
                            //'lead_attendees' => $leadAttendees
                        ];

                        CreateCalendarEvent::run($payload);

                    }
                }
            }
        }
    }
}
