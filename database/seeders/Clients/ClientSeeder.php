<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Actions\CreateClient;
use App\Enums\ClientServiceEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($rapid_seed = (env('RAPID_SEED', false) === true)) {
            $clients = ['The Kalamazoo' => 1];
        } else {
            $clients = [
                'Stencils' => 1,
                'The Z' => 1,
                'Sci-Fi Purple Gyms' => 1,
                'FitnessTruth' => 1,
            ];
        }


        if (! App::environment(['production', 'staging']) && ! $rapid_seed) {
            $clients['TruFit Athletic Clubs'] = 1;
        }


        $services = array_column(ClientServiceEnum::cases(), 'name');
        foreach ($clients as $name => $active) {
            CreateClient::run([
                'name' => $name,
                'active' => $active,
                'services' => $services,
            ]);
        }
    }
}
