<?php

namespace App\Projectors\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Features\AudienceDetails;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaignDetails;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaignDetails;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplateDetails;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplateDetails;
use App\Models\Comms\SmsTemplates;
use App\Models\Team;
use App\Models\User;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateUnAssignedFromSMSCampaign;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientAccountProjector extends Projector
{
    public function onDefaultClientTeamCreated(DefaultClientTeamCreated $event)
    {
        $default_team_name = $event->team;
        $team = Team::create([
            'user_id' => 1,
            'name' => $default_team_name,
            'personal_team' => 0
        ]);
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'default-team',
            'value' => $default_team_name
        ]);
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'team',
            'value' => $team->id
        ]);

        ClientAggregate::retrieve($event->client)
            ->addTeam($team->id, $default_team_name)
            ->addCapeAndBayAdminsToTeam($team->id)
            ->persist();
    }

    public function onCapeAndBayUsersAssociatedWithClientsNewDefaultTeam(CapeAndBayUsersAssociatedWithClientsNewDefaultTeam $event)
    {
        $users = User::whereIn('id', $event->payload)->get();
        $team = Team::find($event->team);

        foreach ($users as $newTeamMember) {
            $team->users()->attach(
                $newTeamMember, ['role' => 'Admin']
            );
        }
    }

    public function onEmailTemplateCreated(EmailTemplateCreated $event)
    {
        // Make Email Details Record
        $template = EmailTemplates::find($event->template);
        $detail = EmailTemplateDetails::create([
            'email_template_id' => $event->template,
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

        // also set the email provider gateway slug
        EmailTemplateDetails::create([
            'email_template_id' => $event->template,
            'client_id' => $event->client,
            'detail' => 'email_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The Email Provider was set to CnB Mailgun and will be billed.']
        ]);

        // make client_details record
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'email_template',
            'value' => $template->id,
        ]);

        /*
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'email_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The Email Provider was set to CnB Mailgun and will be billed.']
        ]);
        */
    }

    public function onEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        $user = User::find($event->updated);
        EmailTemplateDetails::create([
            'email_template_id' => $event->template,
            'client_id' => $event->client,
            'detail' => 'updated',
            'value' => $event->updated,
            'misc' => [
                'old' => $event->old,
                'new' => $event->new,
                'msg' => 'Template was updated by ' . $user->name . ' on ' . date('Y-m-d')
            ]
        ]);
    }

    public function onEmailCampaignCreated(EmailCampaignCreated $event)
    {
        // Make Email Details Record
        $template = EmailCampaigns::find($event->template);
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->template,
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

        // make client_details record
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'email_campaign',
            'value' => $template->id,
        ]);
    }

    public function onEmailCampaignUpdated(EmailCampaignUpdated $event)
    {
        $template = EmailCampaigns::find($event->campaign);
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'client_id' => $event->client,
            'detail' => 'updated',
            'value' => $event->field,
            'misc' => [
                'new' => $event->new,
                'old' => $event->old
            ]
        ]);

        $misc = $detail->misc;
        if ($event->updated == 'auto') {
            $misc['msg'] = 'Campaign was auto-updated';

        } else {
            $user = User::find($event->updated);
            $misc['msg'] = 'Campaign was updated by ' . $user->name . ' on ' . date('Y-m-d');
        }

        $misc['user'] = $event->updated;
        $detail->misc = $misc;
        $detail->save();

//        if (in_array($event->field, ['schedule', 'schedule_date'])) {
        $detail = EmailCampaignDetails::firstOrCreate([
            'email_campaign_id' => $event->campaign,
            'client_id' => $event->client,
            'detail' => $event->field,
        ]);

        $detail->value = $event->new;
        $detail->save();
//        }

    }

    public function onEmailTemplateAssignedToEmailCampaign(EmailTemplateAssignedToEmailCampaign $event)
    {
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'detail' => 'template_assigned',
            'value' => $event->template,
            'client_id' => $event->client
        ]);

        if ($event->user == 'auto') {
            $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $event->user];
        } else {
            $user = User::find($event->user);
            $detail->misc = ['msg' => 'Template was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
        }
        $detail->save();
    }

    public function onEmailTemplateUnAssignedFromEmailCampaign(EmailTemplateUnAssignedFromEmailCampaign $event)
    {
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'detail' => 'template_unassigned',
            'value' => $event->template,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client
        ]);

        $campaign = EmailCampaigns::whereId($event->campaign)
            ->with('unassigned_template')->first();
        if (!is_null($campaign->unassigned_template ?? null)) {
            $campaign->unassigned_template->delete();
        }
    }

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
            'misc' => ['msg' => 'The SMS Provider was set to CnB Twilio and will be billed.']
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
                'msg' => 'Template was updated by ' . $user->name . ' on ' . date('Y-m-d')
            ]
        ]);
    }

    public function onSMSCampaignCreated(SMSCampaignCreated $event)
    {
        // Make Email Details Record
        $template = SmsCampaigns::find($event->template);
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->template,
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

        // make client_details record
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'sms_campaign',
            'value' => $template->id,
        ]);
    }

    public function onSmsCampaignUpdated(SmsCampaignUpdated $event)
    {
        $template = SmsCampaigns::find($event->campaign);
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'client_id' => $event->client,
            'detail' => 'updated',
            'value' => $event->new,
            'misc' => [
                'old' => $event->old
            ]
        ]);

        $misc = $detail->misc;
        if ($event->updated == 'auto') {
            $misc['msg'] = 'Campaign was auto-updated';

        } else {
            $user = User::find($event->updated);
            $misc['msg'] = 'Campaign was updated by ' . $user->name . ' on ' . date('Y-m-d');
        }
        $misc['user'] = $event->updated;
        $detail->misc = $misc;
        $detail->save();

//        if (in_array($event->field, ['schedule', 'schedule_date'])) {
        $detail = SmsCampaignDetails::firstOrCreate([
            'sms_campaign_id' => $event->campaign,
            'client_id' => $event->client,
            'detail' => $event->field,
        ]);

        $detail->value = $event->new;
        $detail->save();

    }

    public function onSMSTemplateAssignedToSMSCampaign(SMSTemplateAssignedToSMSCampaign $event)
    {
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'detail' => 'template_assigned',
            'value' => $event->template,
            'client_id' => $event->client
        ]);

        if ($event->user == 'auto') {
            $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $event->user];
        } else {
            $user = User::find($event->user);
            $detail->misc = ['msg' => 'Template was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
        }
        $detail->save();
    }

    public function onSMSTemplateUnAssignedFromSMSCampaign(SMSTemplateUnAssignedFromSMSCampaign $event)
    {
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'detail' => 'template_unassigned',
            'value' => $event->template,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client
        ]);

        $campaign = SmsCampaigns::whereId($event->campaign)
            ->with('unassigned_template')->first();
        if (!is_null($campaign->unassigned_template)) {
            $campaign->unassigned_template->delete();
        }
    }

    public function onAudienceCreated(AudienceCreated $event)
    {
        $audience = CommAudience::create([
            'client_id' => $event->client,
            'name' => $event->name,
            'slug' => $event->slug,
            'created_by_user_id' => $event->user
        ]);

        AudienceDetails::create([
            'client_id' => $event->client,
            'audience_id' => $audience->id,
            'detail' => 'created',
            'value' => $event->user
        ]);
    }

    public function onAudienceAssignedToEmailCampaign(AudienceAssignedToEmailCampaign $event)
    {
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'detail' => 'audience_assigned',
            'value' => $event->audience,
            'client_id' => $event->client
        ]);

        if ($event->user == 'auto') {
            $detail->misc = ['msg' => 'Audience was auto-assigned', 'user' => $event->user];
        } else {
            $user = User::find($event->user);
            $detail->misc = ['msg' => 'Audience was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
        }
        $detail->save();
    }

    public function onAudienceAssignedToSmsCampaign(AudienceAssignedToSmsCampaign $event)
    {
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'detail' => 'audience_assigned',
            'value' => $event->audience,
            'client_id' => $event->client
        ]);

        if ($event->user == 'auto') {
            $detail->misc = ['msg' => 'Audience was auto-assigned', 'user' => $event->user];
        } else {
            $user = User::find($event->user);
            $detail->misc = ['msg' => 'Audience was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
        }
        $detail->save();
    }

    public function onAudienceUnAssignedFromEmailCampaign(AudienceUnAssignedFromEmailCampaign $event)
    {
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'detail' => 'audience_unassigned',
            'value' => $event->audience,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client
        ]);

        $campaign = EmailCampaigns::whereId($event->campaign)
            ->with('unassigned_audience')->first();
        if (!is_null($campaign->unassigned_audience ?? null)) {
            $campaign->unassigned_audience->delete();
        }
    }

    public function onAudienceUnAssignedFromSmsCampaign(AudienceUnAssignedFromSmsCampaign $event)
    {
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'detail' => 'audience_unassigned',
            'value' => $event->audience,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client
        ]);

        $campaign = SmsCampaigns::whereId($event->campaign)
            ->with('unassigned_audience')->first();
        if (!is_null($campaign->unassigned_audience ?? null)) {
            $campaign->unassigned_audience->delete();
        }
    }


}
