<?php

namespace App\Projectors\Clients;

use App\StorableEvents\Clients\Activity\GatewayProviders\SMS\UserSentATestSMS;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientsBillingActivityProjctor extends Projector
{
    public function onUserSentATestSMS(UserSentATestSMS $event)
    {
        /**
         * @todo STEPS
         * @todo - make an sms_transactions table, model and migration
         * 1. Cut a record in this new table
         * 2. Use this new record as an entity to cut a record into the client_billable_activities table
         */
    }
}
