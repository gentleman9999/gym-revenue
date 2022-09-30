<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Users\Models\User;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Endusers\AudienceMember;
use App\Models\GatewayProviders\GatewayProvider;
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
        $gateway = null;
        $campaign = EmailCampaigns::with(['schedule', 'assigned_template', 'assigned_audience'])
            ->findOrFail($email_campaign_id);
        foreach ($campaign->assigned_template as $assigned_template) {
            $templates[] = EmailTemplate::with('gateway')->findOrFail($assigned_template->value);
        }
        foreach ($templates as $template) {
            $gatewayIntegrations[] = ClientGatewayIntegration::whereNickname($template->gateway->value)->whereClientId($campaign->client_id)->firstOrFail();
        }
        foreach ($gatewayIntegrations as $gatewayIntegration) {
            $gateway = GatewayProvider::findOrFail($gatewayIntegration->gateway_id);
        }

        foreach ($campaign->assigned_audience as $assigned_audience) {
            $audiences[] = CommAudience::findOrFail($assigned_audience->value);
        }
        foreach ($audiences as $audience) {
            $audience_members[] = AudienceMember::whereAudienceId($audience->id)->get();
        }

        $client_aggy = ClientAggregate::retrieve($campaign->client_id);
        $recipients = [];
        $sent_to = [];
        foreach ($audience_members as $audience_member_breakdown) {
            foreach ($audience_member_breakdown as $audience_member) {
                $entity = null;
                switch ($audience_member->entity_type) {
                    case 'user':
                        $entity = User::find($audience_member->entity_id);

                        break;
                    default:
                        //todo:report error - unknown entity_Type
                        break;
                }
                if ($entity) {
                    $recipients[$entity->email] = ['email' => $entity->email, 'name' => $entity->name];
                    $sent_to[] = [
                        'entity_type' => $audience_member->entity_type,
                        'entity_id' => $audience_member->entity_id,
                        'email' => $entity->email,
                        'gateway' => $gateway,
                    ];
                }
            }
        }
        $sent_to_chunks = array_chunk($sent_to, $this->batchSize);
        $idx = 0;
        foreach (array_chunk($recipients, $this->batchSize, true) as $chunk) {
            if (! AppState::isSimuationMode()) {
                $client_aggy->emailSent($email_campaign_id, $sent_to_chunks[$idx], Carbon::now(), true)->persist();
            }
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
