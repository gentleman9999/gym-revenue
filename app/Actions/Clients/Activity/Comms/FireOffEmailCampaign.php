<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Mail\EmailCampaignMail;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Comms\EmailTemplates;
use App\Models\Endusers\AudienceMember;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class FireOffEmailCampaign
{
    use AsAction;

    public string $commandSignature = 'email-campaigns:fire {email_campaign_id}';
    public string $commandDescription = 'Fires off the emails for a given email_campaign_id.';

    public function handle(string $email_campaign_id)
    {
        $campaign = EmailCampaigns::with(['schedule', 'assigned_template', 'assigned_audience'])->findOrFail($email_campaign_id);
        $template = EmailTemplates::with('gateway')->findOrFail($campaign->assigned_template->value);
        $markup = $template->markup;
        $audience = CommAudience::findOrFail($campaign->assigned_audience->value);
//        $audience_members = AudienceMember::whereAudienceId($audience)->get();
        $gatewayIntegration = ClientGatewayIntegration::whereNickname($template->gateway->value)->whereClientId($campaign->client_id)->firstOrFail();
        $gateway = GatewayProvider::findOrFail($gatewayIntegration->gateway_id);
        $audience_members = [['name'=> 'Philip Krogel', 'email' => 'philip@capeandbay.com']];
        $subject = 'Email Campaign Test';
        if(env('ENABLE_MAIL', true)){
            foreach($audience_members as $person){
                $data = ['name' => $person['name']];
                Mail::to('philip@capeandbay.com')->send(new EmailCampaignMail($subject, $markup, $data));
            }
        }
    }
    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            $command->argument('email_campaign_id')
        );
        if(env('ENABLE_MAIL', true)){
            $command->info('Email Sent!');
        }else{
            $command->info('Email Campaign Email skipped because env.ENABLE_MAIL is set to false');
        }
    }
}
