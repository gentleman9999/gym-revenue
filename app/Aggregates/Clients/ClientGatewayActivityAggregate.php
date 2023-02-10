<?php

declare(strict_types=1);

namespace App\Aggregates\Clients;

use App\StorableEvents\Clients\Activity\GatewayProviders\Email\UserSentATestEmail;
use App\StorableEvents\Clients\Activity\GatewayProviders\SMS\UserSentATestSMS;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientGatewayActivityAggregate extends AggregateRoot
{
    protected array $history = [];

    public function applyUserSentATestSMS(UserSentATestSMS $event): void
    {
        $this->history[] = [
            'user_id' => $event->user,
            'event' => 'sms-transmission',
            'value' => $event->template,
        ];
    }

    public function applyUserSentATestEmail(UserSentATestEmail $event): void
    {
        $this->history[] = [
            'user_id' => $event->user,
            'event' => 'email-transmission',
            'value' => $event->template,
        ];
    }

    public function sendATestSMSMessage(string $templateId, string $user_id)
    {
        $this->recordThat(new UserSentATestSMS($this->uuid(), $user_id, $templateId));

        return $this;
    }

    public function sendATestEmailMessage(string $subject, string $templateId, string $user_id)
    {
        $this->recordThat(new UserSentATestEmail($this->uuid(), $user_id, $subject, $templateId));

        return $this;
    }
}
