<?php

namespace App\Projectors\Clients;

use App\Domain\Clients\Models\ClientDetail;
use App\Domain\Users\Models\User;
use App\Models\Clients\ClientBillableActivity;
use App\Models\Comms\SmsTemplateDetails;
use App\Models\Comms\SmsTemplates;
use App\StorableEvents\Clients\Activity\Campaigns\EmailSent;
use App\StorableEvents\Clients\Activity\Campaigns\SmsSent;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientAccountProjector extends Projector
{
    public function onSMSTemplateCreated(SMSTemplateCreated $event)
    {
        // Make Email Details Record
        $template = SmsTemplates::find($event->template);
        $detail = SmsTemplateDetails::create([
            'sms_template_id' => $event->template,
            'client_id' => $event->client,
            'detail' => 'created',
            'value' => $event->created,
        ]);
        if ($event->created == 'auto') {
            $detail->misc = ['msg' => 'Template was auto-generated'];
        } else {
            $user = User::find($event->created);
            $detail->misc = ['msg' => 'Template was created by ' . $user->name . ' on ' . date('Y-m-d')];
        }

        SmsTemplateDetails::create([
            'sms_template_id' => $event->template,
            'client_id' => $event->client,
            'detail' => 'sms_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The SMS Provider was set to CnB Twilio and will be billed.'],
        ]);

        // make client_details record
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'sms_template',
            'value' => $template->id,
        ]);
        /*
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'sms_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The SMS Provider was set to CnB Twilio and will be billed.']
        ]);
        */
    }

    public function onSmsTemplateUpdated(SmsTemplateUpdated $event)
    {
        $user = User::find($event->updated);
        SmsTemplateDetails::create([
            'sms_template_id' => $event->template,
            'client_id' => $event->client,
            'detail' => 'updated',
            'value' => $event->updated,
            'misc' => [
                'old' => $event->old,
                'new' => $event->new,
                'msg' => 'Template was updated by ' . $user->name . ' on ' . date('Y-m-d'),
            ],
        ]);
    }

    public function onSmsSent(SmsSent $event)
    {
        if ($event->isCampaign == true) {
            $launch = SmsCampaigns::with('launched')->find($event->campaign)->launched;
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

    public function onEmailSent(EmailSent $event)
    {
        $launch = EmailCampaigns::with('launched')->find($event->campaign)->launched;
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
