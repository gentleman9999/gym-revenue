<?php

namespace Database\Seeders\Data;

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Projections\Contract;
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
                $users = $client->employees->toArray();
                $categories = AgreementCategory::whereClientId($client->id)->get()->toArray();


                foreach ([$client->members->pluck('id')->toArray(), $client->customers->pluck('id')->toArray()] as $i => $endusers) {
                    $contract_name = $i == 0 ? 'Basic-Membership' : 'Basic-Personal_Training';
                    $contract_id = Contract::whereClientId($client->id)->whereName($contract_name)->first()->id;
                    // For each client, get all the locations
                    if (count($client->locations) > 0) {
                        foreach ($client->locations as $idx => $location) {
                            VarDumper::dump('Generating Agreements for ' . $client->name . '!');
                            for ($x = 0; $x <= $amountOfAgreements; $x++) {
                                $enduser_id = $endusers[array_rand($endusers, 1)];
                                $agreement_data['client_id'] = $client->id;
                                $agreement_data['agreement_category_id'] = $categories[array_rand($categories, 1)]['id'];
                                $agreement_data['gr_location_id'] = $location->gymrevenue_id;
                                $agreement_data['created_by'] = $users[array_rand($users, 1)]['id'];
                                $agreement_data['end_user_id'] = $enduser_id;
                                $agreement_data['agreement_template_id'] = AgreementTemplate::whereClientId($client->id)->first()->id;
                                $agreement_data['active'] = $i == 0;
                                $agreement_data['billing_schedule_id'] = BillingSchedule::first()->id;
                                $agreement_data['contract_id'] = $contract_id;
                                CreateAgreement::run($agreement_data);
                            }
                        }
                    }
                }
            }
        }
    }
}
