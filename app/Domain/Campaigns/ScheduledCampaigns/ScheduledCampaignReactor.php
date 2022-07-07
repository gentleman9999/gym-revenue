<?php

namespace App\Domain\Campaigns\ScheduledCampaigns;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\ScheduledCampaigns\Actions\PublishScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\Actions\UnpublishScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCreated;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignUpdated;
use App\Domain\Reminders\Events\ScheduledCampaignLaunched;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ScheduledCampaignReactor extends Reactor
{
    public function onScheduledCampaignCreated(ScheduledCampaignCreated $event): void
    {
        $scheduledCampaign = ScheduledCampaign::findOrFail($event->aggregateRootUuid());

        $this->maybePublishOrUnpublish($scheduledCampaign, $event);
    }

    public function onScheduledCampaignUpdated(ScheduledCampaignUpdated $event): void
    {
        $scheduledCampaign = ScheduledCampaign::findOrFail($event->aggregateRootUuid());

        $this->maybePublishOrUnpublish($scheduledCampaign, $event);
    }

    /**
     * Fires off a PublishScheduledCampaign or UnpublishScheduledCampaign action
     * if necessary.
     * @param ScheduledCampaign $scheduledCampaign
     * @param ScheduledCampaignCreated|ScheduledCampaignUpdated $event
     * @return void
     */
    protected function maybePublishOrUnpublish(ScheduledCampaign $scheduledCampaign, ScheduledCampaignCreated|ScheduledCampaignUpdated $event)
    {
        if (! array_key_exists('is_published', $event->payload)) {
            //is_published not even provided in the input, don't do anything
            return;
        }
        $isPublished = $event->payload['is_published'];

        if ($isPublished === $scheduledCampaign->is_published) {
            //no change, don't do anything
            return;
        }
        if ($isPublished) {
            PublishScheduledCampaign::run($scheduledCampaign);

            return;
        }
        UnpublishScheduledCampaign::run($scheduledCampaign);
    }

    public function onScheduledCampaignLaunched(ScheduledCampaignLaunched $event): void
    {
        $scheduledCampaign = ScheduledCampaign::with('audience')->findOrFail($event->aggregateRootUuid());
        if ($scheduledCampaign->status === CampaignStatusEnum::ACTIVE) {
            if ($scheduledCampaign->template_type === EmailTemplates::class) {
                //TODO: fire off emails with the given template to the given audience members
                //this needs to be done in a way that we are able to track delivery status back to the campaign
            } elseif ($scheduledCampaign->template_type === SmsTemplates::class) {
                //TODO: fire off sms with the given template to the given audience members
                //this needs to be done in a way that we are able to track delivery status back to the campaign
            } else {
                //TODO:throw exception so we catch this bug
            }
        }
    }

    protected function handleDispatchingEmails(ScheduledCampaign $scheduledCampaign)
    {
//        //TODO: user enums for provider type...
//        $gateway_provider_type = GatewayProviderType::whereName('email')->whereActive(1)->first();
//        $client_gateway = ClientGatewayIntegration::whereClientId($scheduledCampaign->client_id)->whereProviderType($gateway_provider_type->id)->first();
//        //TODO: if $client_gateway null, fallback to cnb gateway
//        $gateway = null;
//            $client_gateway = ClientGatewayIntegration::whereNickname($template->gateway->value)->whereClientId($scheduledCampaign->client_id)->firstOrFail();
//        foreach ($gatewayIntegrations as $gatewayIntegration) {
//            $gateway = GatewayProvider::findOrFail($gatewayIntegration->gateway_id);
//        }

        //in order for a scheduledCampaign to know its complete, we will have to keep track of the generated audience,
        //and then as gateways providers provide delivery status updates, once all of the ids have had a failed/sent
        //status attached by the provider, we mark as complete. we don't want to generate the audience list until
        //right before we dispatch the emails to the provider,
        $recipients = $scheduledCampaign->audience()->getEmailable();

        $client_aggy = ClientAggregate::retrieve($scheduledCampaign->client_id);

        $sent_to_chunks = array_chunk($sent_to, 100);//TODO: see what is max and/or optimal value, make it configurable
        $idx = 0;
        foreach (array_chunk($recipients, $this->batchSize, true) as $chunk) {
            ///TODO: call email batch send job, which checks gateway, and dispatches on right one.
            $idx++;
        }
    }
}
