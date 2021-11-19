<?php

namespace App\Projectors\Clients;

use App\Actions\Jetstream\AddTeamMember;
use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\ClientDetail;
use App\Models\Comms\EmailTemplateDetails;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplateDetails;
use App\Models\Comms\SmsTemplates;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
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

        foreach($users as $newTeamMember)
        {
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
        if($event->created == 'auto')
        {
            $detail->misc = ['msg' => 'Template was auto-generated'];
        }
        else
        {
            $user = User::find($event->created);
            $detail->misc = ['msg' => 'Template was created by '.$user->name.' on '.date('Y-m-d')];
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

        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'email_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The Email Provider was set to CnB Mailgun and will be billed.']
        ]);
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
        if($event->created == 'auto')
        {
            $detail->misc = ['msg' => 'Template was auto-generated'];
        }
        else
        {
            $user = User::find($event->created);
            $detail->misc = ['msg' => 'Template was created by '.$user->name.' on '.date('Y-m-d')];
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

        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'sms_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The SMS Provider was set to CnB Twilio and will be billed.']
        ]);
    }
}
