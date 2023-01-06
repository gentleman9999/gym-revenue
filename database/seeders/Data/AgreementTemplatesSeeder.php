<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

use App\Domain\AgreementTemplates\Actions\CreateAgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Enums\AgreementAvailabilityEnum;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class AgreementTemplatesSeeder extends Seeder
{
    public function run()
    {
        $amountOfAgreements = 10;
        if (env('QUICK_SEED')) {
            $amountOfAgreements = 1;
        }
        // Get all the Clients
        $agreement_availability_random = AgreementAvailabilityEnum::asArray();
        $clients = Client::with('locations')->whereActive(1)
            ->get();
        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Generating Agreement Templates for ' . $client->name . '!');
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        for ($x = 0; $x <= $amountOfAgreements; $x++) {
                            $agreement_data['client_id'] = $client->id;
                            $agreement_data['gr_location_id'] = $location->gymrevenue_id;
                            $agreement_data['agreement_name'] = 'test contract for '.$client->name;
                            $agreement_data['agreement_json'] = json_encode($client);
                            $agreement_data['is_not_billable'] = rand(0, 1);
                            $agreement_data['availability'] = $agreement_availability_random[array_rand($agreement_availability_random)];
                            CreateAgreementTemplate::run($agreement_data);
                        }
                    }
                }
            }
        }
    }
}
