<?php

namespace Database\Seeders\Data;

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class AgreementsSeeder extends Seeder
{
    public function run()
    {
        $amountOfAgreements = 10;
        if (env('QUICK_SEED')) {
            $amountOfAgreements = 1;
        }
        VarDumper::dump('Getting Clients');
        // Get all the Clients
        $clients = Client::whereActive(1)
            ->get();
        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump($client->name);
                $users = User::whereClientId($client->id)->get()->toArray();
                $endusers = EndUser::whereClientId($client->id)->get()->toArray();
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        VarDumper::dump('Generating Agreements for ' . $client->name . '!');
                        for ($x = 0; $x <= $amountOfAgreements; $x++) {
                            $enduser = $endusers[array_rand($endusers, 1)];
                            $agreement_data['client_id'] = $client->id;
                            $agreement_data['gr_location_id'] = $location->gymrevenue_id;
                            $agreement_data['created_by'] = $users[array_rand($users, 1)]['id'];
                            $agreement_data['end_user_id'] = $enduser['id'];
                            $agreement_data['agreement_template_id'] = AgreementTemplate::whereClientId($client->id)->first()->id;
                            $agreement_data['active'] = true;
                            CreateAgreement::run($agreement_data);
                        }
                    }
                }
            }
        }
    }
}
