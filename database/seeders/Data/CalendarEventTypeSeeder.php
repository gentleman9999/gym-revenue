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

            $types = [
                'Tour' => ['name' => 'Tour', 'color' => 'red'],
                'Sales Meeting' => ['name' => 'Sales Meeting', 'color' => 'yellow'],
                'Unavailable' => ['name' => 'Unavailable', 'color' => 'purple'],
                'Training and Development' => ['name' => 'Training and Development', 'color' => 'white'],
                'Prospecting' => ['name' => 'Prospecting', 'color' => 'grey'],
                'External Event' => ['name' => 'External Event', 'color' => 'blue'],
                'Task Follow-Up' => ['name' => 'Task Follow-Up', 'color' => 'green'],
                'Task' => ['name' => 'Task', 'color' => 'green'],
            ];

            if (count($clients) > 0) {
                foreach ($clients as $client) {
                    VarDumper::dump('Creating Calendar Event Types for '.$client->name);
                    foreach ($types as $type) {
                        $payload = [
                            'client_id' => $client->id,
                            'name' => $type['name'].' Event Type for '.$client->name,
                            'description' => $type['name'].' Event Type for '.$client->name,
                            'type' => $type['name'],
                            'color' => $type['color'],
                        ];

                        CalendarAggregate::retrieve($client->id)
                            ->createCalendarEventType('Auto Generated', $payload)
                            ->persist();
                    }
                }
            }
        }
    }
}
