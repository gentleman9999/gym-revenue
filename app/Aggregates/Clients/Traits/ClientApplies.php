<?php

namespace App\Aggregates\Clients\Traits;

use App\Domain\Teams\Events\TeamCreated;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\StorableEvents\Clients\Activity\GatewayProviders\GatewayIntegrationCreated;
use App\StorableEvents\Clients\Activity\Users\ClientUserStoppedBeingImpersonated;
use App\StorableEvents\Clients\Activity\Users\ClientUserWasImpersonated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;

trait ClientApplies
{
    protected string $date_format = 'Y-m-d H:i:s';

    public function applyTeamCreated(TeamCreated $event)
    {
        $this->teams[$event->payload['id']] = $event->payload['name'];
        if ($event->payload['home_team'] ?? false) {
            $this->home_team = $event->payload['id'];
        }
    }

    public function applyEmailTemplateCreated(EmailTemplateCreated $event)
    {
        $history = [
            'type' => 'Email Template Created',
            'template_id' => $event->data['id'],
            'model' => EmailTemplates::class,
            'date' => date($this->date_format, strtotime($event->createdAt())),
        ];
        $history['by'] = $event->user;
        $this->comm_history[] = $history;
    }

    public function applyEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        $history = [
            'type' => 'Email Template Updated',
            'template_id' => $event->data['id'],
            'model' => EmailTemplates::class,
            'date' => date($this->date_format, strtotime($event->createdAt())),
        ];
        $history['by'] = $event->user;
        $this->comm_history[] = $history;
    }

    public function applySMSTemplateCreated(SMSTemplateCreated $event)
    {
        $history = [
            'type' => 'SMS Template Created',
            'template_id' => $event->template,
            'model' => SmsTemplates::class,
            'date' => date($this->date_format, strtotime($event->createdAt())),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }

    public function applySmsTemplateUpdated(SmsTemplateUpdated $event)
    {
        $history = [
            'type' => 'SMS Template Updated',
            'template_id' => $event->template,
            'model' => SmsTemplates::class,
            'date' => date($this->date_format, strtotime($event->createdAt())),
        ];
        $history['by'] = $event->updated;
        $this->comm_history[] = $history;
    }

    public function applyGatewayIntegrationCreated(GatewayIntegrationCreated $event)
    {
        $history = [
            'activity' => 'Gateway Integration Created',
            'type' => $event->type,
            'gateway_slug' => $event->slug,
            'date' => date($this->date_format, strtotime($event->createdAt())),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->provider_history[] = $history;
    }

    public function applyClientUserWasImpersonated(ClientUserWasImpersonated $event)
    {
        $this->employee_activity[] = [
            'event' => 'user-was-impersonated',
            'employee' => $event->employee,
            'date' => $event->date,
            'impersonator' => $event->invader,
        ];
    }

    public function applyClientUserStoppedBeingImpersonated(ClientUserStoppedBeingImpersonated $event)
    {
        $this->employee_activity[] = [
            'event' => 'user-stopped-being-impersonated',
            'employee' => $event->employee,
            'date' => $event->date,
            'impersonator' => $event->invader,
        ];
    }
}
