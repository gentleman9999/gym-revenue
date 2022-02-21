<?php

namespace App\Aggregates\Clients;

use App\StorableEvents\Clients\Activity\GatewayProviders\SMS\UserSentATestSMS;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientGatewayActivityAggregate extends AggregateRoot
{
    protected array $history = [];

    public function applyUserSentATestSMS(UserSentATestSMS $event)
    {
        $this->history[] = [
            'user_id' => $event->user,
            'event' => 'sms-transmission',
            'value' => $event->template,
        ];
    }

    public function sendATestSMSMessage(string $templateId, $user_id)
    {
        $this->recordThat(new UserSentATestSMS($this->uuid(), $user_id, $templateId));

        return $this;
    }
}
