<?php

declare(strict_types=1);

namespace Database\Seeders\Contract;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Actions\CreateContract;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ClientContractSeeder extends Seeder
{
    /**
     * This seeder should be creating contracts for agreement templates, not
     * contracts for agreements. contracts for agreements get generated in reactors.
     * @return void
     */
    public function run()
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            VarDumper::dump('Creating Contracts for client '.$client->name);
            $agreement_categories = AgreementCategory::whereClientId($client->id)->get();//TODO:should iterate over AgreementTemplateCategory
            $agreement_categories->each(function (AgreementCategory $agreementCategory) use ($client) {
                $agreement_category_name = str($agreementCategory->name)->replace(' ', '_');
                $contract_data = [
                    'name' => "Basic-{$agreement_category_name}",
                    'client_id' => $client->id,
//                    'agreement_category_id' => $agreementCategory->id,
//                    'agreement_category_name' => $agreementCategory->name,
                    ];
                CreateContract::run($contract_data);
            });
        }
    }
}
