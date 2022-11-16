<?php

namespace Database\Seeders\Data;

use App\Domain\Agreements\AgreementCategories\Actions\CreateAgreementCategory;
use App\Domain\Clients\Projections\Client;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class AgreementCategoriesSeeder extends Seeder
{
    public function run()
    {
        $amountOfAgreementCategories = ['Membership', 'Personal Training'];

        VarDumper::dump('Getting Clients');
        // Get all the Clients
        $clients = Client::whereActive(1)
            ->get();
        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump('Generating Agreement Categories for ' . $client->name . '!');
                foreach ($amountOfAgreementCategories as $category) {
                    $agreement_category_data['client_id'] = $client->id;
                    $agreement_category_data['name'] = $category;
                    CreateAgreementCategory::run($agreement_category_data);
                }
            }
        }
    }
}
