<?php

declare(strict_types=1);

namespace App\Projectors\Clients;

use App\Models\Clients\ClientBillableActivity;
use App\StorableEvents\Clients\Activity\Campaigns\EmailSent;
use App\StorableEvents\Clients\Activity\Campaigns\SmsSent;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientAccountProjector extends Projector
{
    //TODO: remove this once Philip+Blair finish Email/Sms Tracking refactor
    public function onSmsSent(SmsSent $event): void
    {
        if ($event->isCampaign == true) {
            $launch     = SmsCampaigns::with('launched')->find($event->campaign)->launched;
            $launchedBy = null;
            if ($launch) {
                $launchedBy = $launch->value;
            }
            ClientBillableActivity::create([
                'client_id' => $event->client,
                'desc' => 'SMS sent',
                'entity_type' => 'SmsCampaign',
                'entity_id' => $event->campaign,
                'units' => count($event->sentTo),
                'misc' => json_encode(['sent_to' => $event->sentTo]),
                'triggered_by_user_id' => $launchedBy,
            ]);
        } else {
            ClientBillableActivity::create([
                'client_id' => $event->client,
                'desc' => 'SMS sent',
                'entity_type' => 'sms',
                'entity_id' => $event->campaign,
                'units' => count($event->sentTo),
                'misc' => json_encode(['sent_to' => $event->sentTo]),
                'triggered_by_user_id' => '',
            ]);
        }
    }

    public function onEmailSent(EmailSent $event): void
    {
        $launch     = EmailCampaigns::with('launched')->find($event->campaign)->launched;
        $launchedBy = null;
        if ($launch) {
            $launchedBy = $launch->value;
        }
        ClientBillableActivity::create([
            'client_id' => $event->client,
            'desc' => 'Email sent',
            'entity_type' => 'EmailCampaign',
            'entity_id' => $event->campaign,
            'units' => count($event->sentTo),
            'misc' => json_encode(['sent_to' => $event->sentTo]),
            'triggered_by_user_id' => $launchedBy,
        ]);
    }
}
