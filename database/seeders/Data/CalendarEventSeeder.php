<?php

namespace Database\Seeders\Data;

use App\Domain\CalendarEvents\Actions\CreateCalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Services\Process;
use Illuminate\Database\Seeder;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amount_of_events = env('QUICK_SEED') ? 2 : 5;
        $process          = Process::allocate(5);
        /** Modify the below date range to change when the calendar events will populate for testing since time doesn't stand still. */
        $datestart      = strtotime('2022-08-01');
        $dateend        = strtotime('2022-09-31');
        $daystep        = 86400;
        $clients        = Client::with('users')->whereActive(1)->get();
        $locations      = Location::all()->toArray();
        $location_count = count($locations);
        $event_types    = CalendarEventType::whereIn('client_id', $clients->pluck('id')->toArray())
            ->get()
            ->groupBy('client_id');

        foreach ($clients as $client) {
            $user_ids = [];
            foreach ($client->users as $user) {
                $user_ids[] = $user->id;
            }

            $user_ids = array_values(array_unique($user_ids));

            foreach ($event_types[$client->id] as $eventType) {
                for ($i = 1; $i <= $amount_of_events; $i++) {
                    $datebetween = abs(($dateend - $datestart) / $daystep);
                    $randomday = rand(0, $datebetween);
                    $hour1 = rand(3, 12);
                    $hour2 = $hour1 + rand(1, 2);
                    $title = "Test #{$i} for {$client->name}";
                    $start = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                    $end = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour2 . ':00:00';

                    $attendees = [];
                    for ($d = 1; $d <= 2; $d++) {
                        $attendees[] = $user_ids[rand(0, count($user_ids) - 1)];
                    }

                    if ($location_count > 0) {
                        $loc_id = $locations[rand(0, $location_count - 1)]['id'];
                    } else {
                        $loc_id = null;
                    }

                    $process->nameQueue($client->name, [CreateCalendarEvent::class, 'run'], [
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
                    ]);
                }
            }

            echo("Creating Calendar Events for {$client->name}\n");
        }

        $process->run();
    }
}
