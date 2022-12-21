<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Clients\Projections\Client;
use App\Domain\Contracts\Events\ContractCreated;
use App\Services\Contract\AdobeAPIService;
use App\Services\Contract\ClientData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ContractReactor extends Reactor
{
    public function onContractCreated(ContractCreated $event): void
    {
        $template = Cache::get('is_seeding', false) ? 'SeederAgreementTemplate.docx' : 'TestAgreementTemplate.docx';

        $cache_key = $event->clientId() . '-contract_pdf_template-' . $template;

        $cached_pdf = Cache::get($cache_key);

        if ($cached_pdf) {
            Log::info('Found a cached PDF for contract: ' . $event->aggregateRootUuid());
            //TODO: just store a reference to the cached pdf
            return;
        }

        Log::info('Generating a PDF for contract: ' . $event->aggregateRootUuid());
        $adobe_service = new AdobeAPIService();

        $json_key = [
            'client_name',
            'agreement_template_type',
        ];

        $client = Client::find($event->clientId());
        $client_info = new ClientData('Contract', $client->name, $client->id, $template);
        $client_info->setEntityId($event->aggregateRootUuid());
        $client_info->setJsonData($json_key);

        $result = json_decode($adobe_service->generatePDF($client_info));
        Cache::put($cache_key, $result);
        //TODO: we need to save a copy of the generated PDF in s3, and store a ref in the contract table
        //TODO: error handling
//            if (!$result->status) {
////                        UpdateContract::run($contract, ['pdf_file' => $result]);
//                VarDumper::dump("PDF for " . $client->name . ' was not created due to error: ' . $result->message);
//            } else {
//                VarDumper::dump("PDF for " . $client->name . ' is created successfully');
//            }
    }

//    public function onContractUpdated(ContractUpdated $event): void
//    {
//        Contract::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
//    }
}
