<?php

namespace Database\Seeders\Clients;

use App\Actions\Clients\Classifications\CreateClassification;
use App\Domain\Clients\Models\Client;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ClassificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        $classifications = [
            'Club Associate',
            'Floor Manager/Team Lead',
            'Fitness Trainer',
            'Personal Trainer',
            'Instructor',
            'Sanitation',
        ];

        foreach ($clients as $client) {
            VarDumper::dump('Dumping Classifications for '.$client->name);
            foreach ($classifications as $classification) {
                CreateClassification::run([
                    'client_id' => $client->id,
                    'title' => $classification,
                ]);
            }
        }
    }
}
