<?php

declare(strict_types=1);

namespace App\Reactors\Clients\GatewayProviders;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Services\GatewayProviders\SMS\SMSGatewayProviderService;
use App\StorableEvents\Clients\Activity\GatewayProviders\SMS\UserSentATestSMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientSMSGatewayActivityReactor extends Reactor implements ShouldQueue
{
    public function onUserSentATestSMS(UserSentATestSMS $event): void
    {
        $user_aggy = UserAggregate::retrieve($event->user);
        // Initialize the SMS Gateway Service
        $gateway_service = new SMSGatewayProviderService(SmsTemplate::find($event->template));
        // use the service to get the correct gateway & pull the profile
        $gateway_service->initSMSGateway($event->user);

        // Get the user's phone number and use the service to fire the message
        $response = $gateway_service->fire($user_aggy->getPhoneNumber());

        // In all cases do the User aggy to log the action
        $user_aggy->logClientSMSActivity($event->template, $response, $event->client)->persist();
        /**
         * STEPS
         * 7.
         * 8. In client cases, log the action with the client too, so that it's projector can add an invoicing record and activity_history
         */
    }
}
