<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Actions\Mail\MailgunBatchSend;
use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Comms\EmailTemplates;
use App\Models\Endusers\AudienceMember;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\User;
use App\Models\Utility\AppState;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class FireOffEmailCampaign
{
    use AsAction;

    public string $commandSignature = 'email-campaigns:fire {email_campaign_id}';
    public string $commandDescription = 'Fires off the emails for a given email_campaign_id.';

    private int $batchSize = 100;//MAX IS 1000

    public function handle(string $email_campaign_id)
    {
        $campaign = EmailCampaigns::with(['schedule', 'assigned_template', 'assigned_audience'])->findOrFail($email_campaign_id);
        $template = EmailTemplates::with('gateway')->findOrFail($campaign->assigned_template->value);

        foreach ($campaign->assigned_audience as $assigned_audience)
        {
            $audiences[] = CommAudience::findOrFail($assigned_audience->value);
        }
        foreach ($audiences as $audience)
        {
            $audience_members[] = AudienceMember::whereAudienceId($audience->id)->get();
        }
        $gatewayIntegration = ClientGatewayIntegration::whereNickname($template->gateway->value)->whereClientId($campaign->client_id)->firstOrFail();
        $gateway = GatewayProvider::findOrFail($gatewayIntegration->gateway_id);
        $client_aggy = ClientAggregate::retrieve($campaign->client_id);
        $recipients = [];
        $sent_to = [];
        foreach ($audience_members as $audience_member_breakdown)
        {
            foreach ($audience_member_breakdown as $audience_member)
            {
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
                    //TODO: probably don't just serialize the $member object. we need to pluck what we want, and merge with other variables
                    $recipients[$member->email] = ['email' => $member->email, 'name' => $member->name];
                    $sent_to[] = ['entity_type' => $audience_member->entity_type, 'entity_id' => $audience_member->entity_id, 'email' => $member->email];
                }
            }
        }
        $sent_to_chunks = array_chunk($sent_to, $this->batchSize);
        $idx = 0;
        foreach (array_chunk($recipients, $this->batchSize, true) as $chunk) {
            if (!AppState::isSimuationMode()) {
                MailgunBatchSend::dispatch($chunk, $template->subject, $template->markup);
            }
            $client_aggy->logEmailsSent($email_campaign_id, $sent_to_chunks[$idx], Carbon::now())->persist();
            $idx++;
        }


        $client_aggy->completeEmailCampaign($email_campaign_id, Carbon::now())->persist();
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            $command->argument('email_campaign_id')
        );
        if (AppState::isSimuationMode()) {
            $command->info('Email Campaign skipped sending email because app is in simulation mode');
        } else {
            $command->info('Emails Sent!');
        }
    }
}
