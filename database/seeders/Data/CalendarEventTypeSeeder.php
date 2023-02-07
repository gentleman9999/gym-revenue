<?php

namespace Database\Seeders\Data;

use App\Domain\CalendarEventTypes\Actions\CreateCalendarEventType;
use App\Domain\Clients\Projections\Client;
use App\Services\Process;
use Illuminate\Database\Seeder;

class CalendarEventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

        $process = Process::allocate(5);
        foreach (Client::whereActive(1)->get() as $client) {
            echo("Creating Calendar Event Types for {$client->name}\n");
            foreach ($types as $type) {
                $process->queue([CreateCalendarEventType::class, 'run'], [
                    'client_id' => $client->id,
                    'name' => $type['name'],
                    'description' => "{$type['name']} Event Type for {$client->name}",
                    'type' => $type['name'],
                    'color' => $type['color'],
                ]);
            }
        }

        $process->run();
    }
}
