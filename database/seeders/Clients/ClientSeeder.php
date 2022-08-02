<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Actions\CreateClient;
use App\Enums\ClientServiceEnum;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = [
            'The Kalamazoo' => 1,
            'iFit' => 1,
            'TruFit Athletic Clubs' => 1,
            'Stencils' => 1,
            'The Z' => 1,
            'Sci-Fi Purple Gyms' => 1,
            'FitnessTruth' => 1,
        ];


        foreach ($clients as $name => $active) {
            $client = CreateClient::run(
                [
                    'name' => $name,
                    'active' => $active,
                    'services' => collect(ClientServiceEnum::cases())->map(fn ($e) => $e->name),
                ]
            );
        }
    }
}
