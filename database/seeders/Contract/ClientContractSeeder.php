<?php

declare(strict_types=1);

namespace Database\Seeders\Contract;

use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Actions\CreateContract;
use Illuminate\Database\Seeder;

class ClientContractSeeder extends Seeder
{
    /**
     * This seeder should be creating contracts for agreement templates, not
     * contracts for agreements. contracts for agreements get generated in reactors.
     * @return void
     */
    public function run()
    {
        foreach (Client::with('agreementCategories')->get() as $client) {
            echo("Creating Contracts for client {$client->name}\n");

            //TODO:should iterate over AgreementTemplateCategory
            foreach ($client->agreementCategories as $category) {
                CreateContract::run([
                    'name' => 'Basic-' . str_replace(' ', '_', $category->name),
                    'client_id' => $client->id,
//                    'agreement_category_id' => $category->id,
//                    'agreement_category_name' => $category->name,
                ]);
            }
        }
    }
}
