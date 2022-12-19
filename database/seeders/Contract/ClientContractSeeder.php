<?php

declare(strict_types=1);

namespace Database\Seeders\Contract;

use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Actions\CreateContract;
use App\Services\Contract\AdobeAPIService;
use App\Services\Contract\ClientData;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ClientContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::with(['agreements','agreements.category','agreements.endUser'])->get();

        foreach ($clients as $client) {
            $amount_of_agreement = count($client->agreements);
            if ($amount_of_agreement > 0) {
                $amount_of_pdf = 2;
                foreach ($client->agreements as $key => $agreement) {
                    if ($key >= $amount_of_pdf) {
                        break;
                    }
                    $client_name = str_replace(' ', '', $client->name);
                    $contract_data = [
                        'name' => "{$client_name}-{$agreement->endUser->first_name}{$agreement->endUser->last_name}-Agreement-".time(),
                        'client_id' => $client->id, ];
                    $contract = CreateContract::run($contract_data);
                    $adobe_service = new AdobeAPIService();

                    $json_key = [
                        'client_name',
                        'agreement_template_type',
                    ];

                    VarDumper::dump("Creating contract PDF for ".$client->name);
                    $client_info = new ClientData('Contract', $client->name, $client->id, 'SeederAgreementTemplate.docx');
                    $client_info->setEndUserId($agreement->end_user_id);
                    $client_info->setEntityId($contract->id);
                    $client_info->setJsonData($json_key);

                    $result = json_decode($adobe_service->generatePDF($client_info));
                    if (! $result->status) {
                        VarDumper::dump("PDF for ".$client->name.' was not created');
                    } else {
                        VarDumper::dump("PDF for ".$client->name.' is created successfully');
                    }
                }
            }
        }
    }
}
