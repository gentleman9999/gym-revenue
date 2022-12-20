<?php

declare(strict_types=1);

namespace Database\Seeders\Contract;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Actions\CreateContract;
use App\Domain\Contracts\Actions\UpdateContract;
use App\Domain\Users\Models\User;
use App\Services\Contract\AdobeAPIService;
use App\Services\Contract\ClientData;
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
            $agreement_categories = AgreementCategory::whereClientId($client->id)->get();//TODO:should iterate over AgreementTemplateCategory
            $agreement_categories->each(function(AgreementCategory $agreementCategory) use ($client) {
                $agreement_category_name = str($agreementCategory->name)->replace(' ', '_');
                $contract_data = [
                    'name' => "Basic-{$agreement_category_name}",
                    'client_id' => $client->id,
//                    'agreement_category_id' => $agreementCategory->id,
//                    'agreement_category_name' => $agreementCategory->name,
                    ];

                $contract = CreateContract::run($contract_data);

                if(!env('RAPID_SEED', false)) {

                    $adobe_service = new AdobeAPIService();

                    $json_key = [
                        'client_name',
                        'agreement_template_type',
                    ];

                    VarDumper::dump("Creating contract PDF for " . $client->name);
                    $client_info = new ClientData('Contract', $client->name, $client->id, 'SeederAgreementTemplate.docx');
                    $client_info->setEntityId($contract->id);
                    $client_info->setJsonData($json_key);

                    $result = json_decode($adobe_service->generatePDF($client_info));
                    if (!$result->status) {
                        VarDumper::dump("PDF for " . $client->name . ' was not created due to error: ' . $result->message);
                    } else {
                        VarDumper::dump("PDF for " . $client->name . ' is created successfully');
                    }
                    UpdateContract::run($contract, ['pdf_file' => $result]);
                    //TODO: Why aren't we storing the PDF file in the database?
                }
            });

        }
    }
}
