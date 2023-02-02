<?php

namespace Database\Seeders;

use App\Domain\Clients\Projections\Client;
use App\Models\Position;
use App\Support\Uuid;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::whereActive(1)->get();
        if (count($clients) === 0) {
            return;
        }

        $items = [
            'Front Desk',
            'Maintenance',
            'Childcare',
            'Group Exercise Instructor',
            'Personal Trainer',
            'Assistant General Manager',
            'General Manager',
            'Fitness Manager',
            'Fitness Director',
            'Sales Manager',
            'Sales Director',
            'Fitness Sales Rep',
        ];

        $data = [];
        foreach ($clients as $client) {
            foreach ($items as $i) {
                $data[] = [
                    'id'        => Uuid::new(),
                    'client_id' => $client->id,
                    'name'      => $i,
                ];
            }
        }

        Position::insert($data);
    }
}
