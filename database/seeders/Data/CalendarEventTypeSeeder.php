<?php

namespace Database\Seeders\Data;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Clients\Client;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class CalendarEventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {
            // Get all the Clients
            VarDumper::dump('Getting Clients');
            $clients = Client::whereActive(1)->get();

            $types = ['Tour', 'Sales Meeting', 'Unavailable', 'Training and Development', 'Prospecting', 'External Event', 'Task Follow-Up'];

            if (count($clients) > 0) {
                foreach ($clients as $client)
                {
                    VarDumper::dump('Creating Events for '.$client->name);
                    foreach ($types as $type)
                    {
                        $aggy = CalendarAggregate::retrieve($client->id)
                            ->createCalendarEventType($type.' for '.$client->name, 'Description for '.$client->name, $type)
                            ->persist();
                    }
                }
            }
        }
    }
}
