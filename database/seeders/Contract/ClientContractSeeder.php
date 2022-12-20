<?php

declare(strict_types=1);

namespace Database\Seeders\Contract;

use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Actions\CreateContract;
use App\Domain\EndUsers\Projections\EndUser;
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
        $clients = Client::all();

        foreach ($clients as $client) {
            $end_user = EndUser::whereClientId($client->id)->first();
            $client_name = str_replace(' ', '', $client->name);
            $contract_data = [
                'name' => "{$client_name}-{$end_user->name}-Agreement-".time(),
                'client_id' => $client->id, ];
            $contract = CreateContract::run($contract_data);

            if(!env('RAPID_SEED', false)) {

                $adobe_service = new AdobeAPIService();

                $json_key = [
                    'client_name',
                    'agreement_template_type',
                ];

                VarDumper::dump("Creating contract PDF for " . $client->name);
                $client_info = new ClientData('Contract', $client->name, $client->id, 'SeederAgreementTemplate.docx');
                $client_info->setEndUserId($end_user->id);
                $client_info->setEntityId($contract->id);
                $client_info->setJsonData($json_key);

                $result = json_decode($adobe_service->generatePDF($client_info));
                if (!$result->status) {
                    VarDumper::dump("PDF for " . $client->name . ' was not created due to error: ' . $result->message);
                } else {
                    VarDumper::dump("PDF for " . $client->name . ' is created successfully');
                }
            }
        }
    }
}
