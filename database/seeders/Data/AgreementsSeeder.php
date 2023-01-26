<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\Agreements\Actions\SignAgreement;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Projections\Contract;
use App\Models\File;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class AgreementsSeeder extends Seeder
{
    public function run()
    {
        $amount_of_agreements = 10;
        if (env('QUICK_SEED')) {
            $amount_of_agreements = 2;
        }
        // Get all the Clients
        $clients = Client::with('employees', 'locations', 'members', 'customers')->whereActive(1)
            ->get();
        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Generating Agreements for ' . $client->name . '!');
                $users = $client->employees()->whereJsonContains('details->default_team_id', '!=', '')
                    ->get()->pluck('id')->toArray();
                dd($users);
                $categories = AgreementCategory::whereClientId($client->id)->get()->toArray();
                $file = File::whereClientId($client->id)->whereFileableType(Contract::class)->first();
                $amount_of_user = count($users);

                //Make half of the users members or customers.
                if ($amount_of_agreements > $amount_of_user) {
                    $amount_of_agreements = ceil($amount_of_user / 2);
                } else {
                    $amount_of_agreements = ceil($amount_of_agreements / 2);
                }

                $endusers = $client->leads()->whereJsonContains('details->membership_type_id', '')
                    ->get()->pluck('id')->toArray();
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        VarDumper::dump('Creating Agreements for ' . $client->name . ' on Location '.$location->name.'!');
                        for ($x = 0; $x <= $amount_of_agreements; $x++) {
                            $enduser_id = $endusers[array_rand($endusers, 1)];
                            $agreement_data['client_id'] = $client->id;
                            $agreement_data['agreement_category_id'] = $categories[array_rand($categories, 1)]['id'];
                            $agreement_data['gr_location_id'] = $location->gymrevenue_id;
                            $agreement_data['created_by'] = $users[array_rand($users, 1)]['id'];
                            $agreement_data['user_id'] = $enduser_id;
                            $agreement_data['agreement_template_id'] = AgreementTemplate::whereClientId($client->id)->first()->id;
                            $agreement_data['active'] = $i == 0;
                            if ($file) {
                                $agreement_data['contract_file_id'] = $file->id;
                            }

                            $agreement = CreateAgreement::run($agreement_data);

                            VarDumper::dump('Signing Agreements for ' . $client->name . '!');
                            $sign_agreement_data['user_id'] = $enduser_id;
                            $sign_agreement_data['active'] = true;
                            SignAgreement::run($sign_agreement_data, $agreement->id);
                        }
                    }
                }
            }
        }
    }
}
