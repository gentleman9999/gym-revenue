<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Mail\EmailCampaignMail;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\QueuedEmailCampaign;
use App\Models\Endusers\AudienceMember;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\User;
use Carbon\Carbon;
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
        $audience_members = AudienceMember::whereAudienceId($audience->id)->get();
        $gatewayIntegration = ClientGatewayIntegration::whereNickname($template->gateway->value)->whereClientId($campaign->client_id)->firstOrFail();
        $gateway = GatewayProvider::findOrFail($gatewayIntegration->gateway_id);
        $subject = 'Email Campaign Test';
        foreach ($audience_members as $audience_member) {
            $member = null;
            switch ($audience_member->entity_type) {
                case 'user':
                    $member = User::find($audience_member->entity_id);
                    break;
                case 'prospect':
//                        $member = Prospects::findOrFail($audience_member->entity_id);
                    break;
                case 'conversion':
//                        $member = Conversions::findOrFail($audience_member->entity_id);
                    break;
                default:
                    //todo:report error - unknown entity_Type
                    break;
            }
            if ($member) {
                if (env('ENABLE_MAIL', true)) {
                    Mail::to($member->email)->send(new EmailCampaignMail($subject, $markup, $member->toArray()));
                }
            }
        }

        $queued_email_campaign = QueuedEmailCampaign::whereEmailCampaignId($email_campaign_id)->first();
        if ($queued_email_campaign) {
            $queued_email_campaign->completed_at = Carbon::now();
            $queued_email_campaign->save();
        }
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            $command->argument('email_campaign_id')
        );
        if (env('ENABLE_MAIL', true)) {
            $command->info('Emails Sent!');
        } else {
            $command->info('Email Campaign skipped sending email because env.ENABLE_MAIL is set to false');
        }
    }
}
