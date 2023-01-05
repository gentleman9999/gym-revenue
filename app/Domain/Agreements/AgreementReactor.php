<?php

declare(strict_types=1);

namespace App\Domain\Agreements;

use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementSigned;
use App\Domain\Agreements\Events\AgreementUpdated;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\Users\Actions\UpdateUser;
use App\Domain\Users\Models\User;
use App\Services\Contract\AdobeAPIService;
use App\Services\Contract\ClientData;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class AgreementReactor extends Reactor implements ShouldQueue
{
    public function onAgreementCreated(AgreementCreated $event): void
    {
        $agreement = Agreement::findOrFail($event->aggregateRootUuid());
        $template_locations = json_decode($agreement->template->agreement_json);
        $location = [];
        if (isset($template_locations->locations)) {
            foreach ($template_locations->locations as $template_location) {
                $location[] = $template_location->address1 . ', ' . $template_location->address2 . ' ' . $template_location->city . ', ' . $template_location->state . '' . $template_location->zip;
            }
        }
        $locations = '<br>' . implode('<br>', $location);
        eval("\$locations = \"$locations\";"); //To pass the locations array in proper format

        $json_key = [
            'client_name',
            'template_type',
            'template_category',
            'date_created',
            'end_user_name',
            'locations',
        ];

        $client_info = new ClientData('Agreement', $agreement->client->name, $agreement->client_id, 'TestAgreementTemplate.docx');
        $client_info->setUserId($agreement->user_id);
        $client_info->setEntityId($event->aggregateRootUuid());
        $client_info->setTemplateCategory($agreement->category->name);
        $client_info->setDateCreated(Carbon::now()->format('Y/m/d'));
        $client_info->setUserName($event->payload['user_id']);
        $client_info->setLocation($locations);
        $client_info->setJsonData($json_key);

        if (! Cache::get('is_seeding', false)) {
            $adobe_service = new AdobeAPIService();
            $adobe_service->generatePDF($client_info);
        }
    }

    public function onAgreementSigned(AgreementSigned $event): void
    {
        $this->updateUserStatus($event);
    }

    public function onAgreementUpdated(AgreementUpdated $event): void
    {
        $this->updateUserStatus($event);
    }

    protected function updateUserStatus(AgreementSigned|AgreementUpdated $event): void
    {
        $user_type = User::determineUserType($event->payload['user_id']);


        UpdateUser::run(
            User::find($event->payload['user_id']),
            [
                'user_type' => $user_type,
            ]
        );
    }
}
