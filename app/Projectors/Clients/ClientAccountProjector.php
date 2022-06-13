<?php

namespace App\Projectors\Clients;

use App\Aggregates\Users\UserAggregate;
use App\Domain\Teams\Models\Team;
use App\Models\Clients\ClientBillableActivity;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Features\AudienceDetails;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaignDetails;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaignDetails;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\QueuedEmailCampaign;
use App\Models\Comms\QueuedSmsCampaign;
use App\Models\Comms\SmsTemplateDetails;
use App\Models\Comms\SmsTemplates;
use App\Models\TeamDetail;
use App\Models\User;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCompleted;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailSent;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignCompleted;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsSent;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateUnAssignedFromSMSCampaign;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use App\StorableEvents\Clients\TeamAttachedToClient;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientAccountProjector extends Projector
{
    public function onTeamAttachedToClient(TeamAttachedToClient $event)
    {
        $team = Team::findOrFail($event->team);

        if ($team->home_team) {
            $team->client->home_team_id = $team->id;
            $team->save();
        }
        foreach ($event->payload['locations'] ?? [] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $team->id, 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }

    public function onCapeAndBayUsersAssociatedWithClientsNewDefaultTeam(CapeAndBayUsersAssociatedWithClientsNewDefaultTeam $event)
    {
        $users = User::whereIn('id', $event->payload)->get();
        $team = Team::find($event->team);

        foreach ($users as $newTeamMember) {
            $team->users()->attach($newTeamMember);
            $team_client = Team::getClientFromTeamId($team->id);
            $team_client_id = ($team_client) ? $team_client->id : null;

            // Since the user needs to have their team added in a single transaction in createUser
            // A projector won't get executed (for now) but an apply function will run on the next retrieval
            UserAggregate::retrieve($newTeamMember->id)
                ->addUserToTeam($team->id, $team->name, $team_client_id)
                ->persist();
        }
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
                'old' => $event->old,
            ],
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

    public function onEmailCampaignLaunched(EmailCampaignLaunched $event)
    {
        EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'client_id' => $event->client,
            'detail' => 'launched',
            'value' => $event->user,
        ]);
    }

    public function onEmailTemplateAssignedToEmailCampaign(EmailTemplateAssignedToEmailCampaign $event)
    {
        try {
            foreach ($event->template as $template) {
                $detail = EmailCampaignDetails::create([
                    'email_campaign_id' => $event->campaign,
                    'detail' => 'template_assigned',
                    'value' => $template,
                    'client_id' => $event->client,
                ]);

                if ($event->user == 'auto') {
                    $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $event->user];
                } else {
                    $user = User::find($event->user);
                    $detail->misc = ['msg' => 'Template was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
                }
                $detail->save();
            }
        } catch (\Exception $e) {
            $detail = EmailCampaignDetails::create([
                'email_campaign_id' => $event->campaign,
                'detail' => 'template_assigned',
                'value' => $event->template,
                'client_id' => $event->client,
            ]);

            if ($event->user == 'auto') {
                $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $event->user];
            } else {
                $user = User::find($event->user);
                $detail->misc = ['msg' => 'Template was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
            }
            $detail->save();
        }
    }

    public function onEmailTemplateUnAssignedFromEmailCampaign(EmailTemplateUnAssignedFromEmailCampaign $event)
    {
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'detail' => 'template_unassigned',
            'value' => $event->template,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client,
        ]);

        $campaign = EmailCampaigns::whereId($event->campaign)
            ->with('unassigned_template')->first();
        if (! is_null($campaign->unassigned_template ?? null)) {
            if (! is_null($campaign->unassigned_template)) {
                if (collect($campaign->unassigned_template)->isNotEmpty()) {
                    foreach ($campaign->unassigned_template as $unassigned_template) {
                        $unassigned_template->delete();
                    }
                }
            }
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
                'old' => $event->old,
            ],
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

    public function onSmsCampaignLaunched(SmsCampaignLaunched $event)
    {
        SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'client_id' => $event->client,
            'detail' => 'launched',
            'value' => $event->user,
        ]);
    }

    public function onSMSTemplateAssignedToSMSCampaign(SMSTemplateAssignedToSMSCampaign $event)
    {
        try {
            foreach ($event->template as $template) {
                $detail = SmsCampaignDetails::create([
                    'sms_campaign_id' => $event->campaign,
                    'detail' => 'template_assigned',
                    'value' => $template,
                    'client_id' => $event->client,
                ]);

                if ($event->user == 'auto') {
                    $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $event->user];
                } else {
                    $user = User::find($event->user);
                    $detail->misc = ['msg' => 'Template was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
                }
                $detail->save();
            }
        } catch (\Exception $e) {
            $detail = SmsCampaignDetails::create([
                'sms_campaign_id' => $event->campaign,
                'detail' => 'template_assigned',
                'value' => $event->template,
                'client_id' => $event->client,
            ]);

            if ($event->user == 'auto') {
                $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $event->user];
            } else {
                $user = User::find($event->user);
                $detail->misc = ['msg' => 'Template was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
            }
            $detail->save();
        }
    }

    public function onSMSTemplateUnAssignedFromSMSCampaign(SMSTemplateUnAssignedFromSMSCampaign $event)
    {
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'detail' => 'template_unassigned',
            'value' => $event->template,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client,
        ]);

        $campaign = SmsCampaigns::whereId($event->campaign)
            ->with('unassigned_template')->first();
        if (! is_null($campaign->unassigned_template ?? null)) {
            if (! is_null($campaign->unassigned_template)) {
                if (collect($campaign->unassigned_template)->isNotEmpty()) {
                    foreach ($campaign->unassigned_template as $unassigned_template) {
                        $unassigned_template->delete();
                    }
                }
            }
        }
    }

    public function onAudienceCreated(AudienceCreated $event)
    {
        $audience = CommAudience::create([
            'client_id' => $event->client,
            'name' => $event->name,
            'slug' => $event->slug,
            'created_by_user_id' => $event->user,
        ]);

        AudienceDetails::create([
            'client_id' => $event->client,
            'audience_id' => $audience->id,
            'detail' => 'created',
            'value' => $event->user,
        ]);
    }

    public function onAudienceAssignedToEmailCampaign(AudienceAssignedToEmailCampaign $event)
    {
        foreach ($event->audience as $audience) {
            $detail = EmailCampaignDetails::create([
                'email_campaign_id' => $event->campaign,
                'detail' => 'audience_assigned',
                'value' => $audience,
                'client_id' => $event->client,
            ]);

            if ($event->user == 'auto') {
                $detail->misc = ['msg' => 'Audience was auto-assigned', 'user' => $event->user];
            } else {
                $user = User::find($event->user);
                $detail->misc = ['msg' => 'Audience was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
            }
            $detail->save();
        }
    }

    public function onAudienceAssignedToSmsCampaign(AudienceAssignedToSmsCampaign $event)
    {
        foreach ($event->audience as $audience) {
            $detail = SmsCampaignDetails::create([
                'sms_campaign_id' => $event->campaign,
                'detail' => 'audience_assigned',
                'value' => $audience,
                'client_id' => $event->client,
            ]);

            if ($event->user == 'auto') {
                $detail->misc = ['msg' => 'Audience was auto-assigned', 'user' => $event->user];
            } else {
                $user = User::find($event->user);
                $detail->misc = ['msg' => 'Audience was assigned by ' . $user->name . ' on ' . date('Y-m-d'), 'user' => $event->user];
            }
            $detail->save();
        }
    }

    public function onAudienceUnAssignedFromEmailCampaign(AudienceUnAssignedFromEmailCampaign $event)
    {
        $detail = EmailCampaignDetails::create([
            'email_campaign_id' => $event->campaign,
            'detail' => 'audience_unassigned',
            'value' => $event->audience,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client,
        ]);

        $campaign = EmailCampaigns::whereId($event->campaign)
            ->with('unassigned_audience')->first();
        if (! is_null($campaign->unassigned_audience ?? null)) {
            if (! is_null($campaign->unassigned_audience)) {
                if (collect($campaign->unassigned_audience)->isNotEmpty()) {
                    foreach ($campaign->unassigned_audience as $unassigned_audience) {
                        $unassigned_audience->delete();
                    }
                }
            }
        }
    }

    public function onAudienceUnAssignedFromSmsCampaign(AudienceUnAssignedFromSmsCampaign $event)
    {
        $detail = SmsCampaignDetails::create([
            'sms_campaign_id' => $event->campaign,
            'detail' => 'audience_unassigned',
            'value' => $event->audience,
            'misc' => ['by' => $event->user],
            'client_id' => $event->client,
        ]);

        $campaign = SmsCampaigns::whereId($event->campaign)
            ->with('unassigned_audience')->first();
        if (! is_null($campaign->unassigned_audience ?? null)) {
            if (! is_null($campaign->unassigned_audience)) {
                if (collect($campaign->unassigned_audience)->isNotEmpty()) {
                    foreach ($campaign->unassigned_audience as $unassigned_audience) {
                        $unassigned_audience->delete();
                    }
                }
            }
        }
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

    public function onSmsCampaignCompleted(SmsCampaignCompleted $event)
    {
        $queued_sms_campaign = QueuedSmsCampaign::whereSmsCampaignId($event->campaign)->first();
        if ($queued_sms_campaign) {
            $queued_sms_campaign->completed_at = $event->date;
            $queued_sms_campaign->save();
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

    public function onEmailCampaignCompleted(EmailCampaignCompleted $event)
    {
        $queued_email_campaign = QueuedEmailCampaign::whereEmailCampaignId($event->campaign)->first();
        if ($queued_email_campaign) {
            $queued_email_campaign->completed_at = $event->date;
            $queued_email_campaign->save();
        }
    }
}
