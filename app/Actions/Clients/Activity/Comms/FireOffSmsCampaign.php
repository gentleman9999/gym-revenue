<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\QueuedSmsCampaign;
use App\Models\Comms\SmsTemplates;
use App\Models\Endusers\AudienceMember;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\User;
use App\Models\UserDetails;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class FireOffSmsCampaign
{
    use AsAction;

    public string $commandSignature = 'sms-campaigns:fire {sms_campaign_id}';
    public string $commandDescription = 'Fires off the SMS for a given sms_campaign_id.';

    protected $tokens = ['name'];

    public function handle(string $sms_campaign_id)
    {
        $campaign = SmsCampaigns::with(['schedule', 'assigned_template', 'assigned_audience'])->findOrFail($sms_campaign_id);
        $template = SmsTemplates::with('gateway')->findOrFail($campaign->assigned_template->value);
        $markup = $template->markup;
        $audience = CommAudience::findOrFail($campaign->assigned_audience->value);
        $audience_members = AudienceMember::whereAudienceId($audience->id)->get();
        $gatewayIntegration = ClientGatewayIntegration::whereNickname($template->gateway->value)->whereClientId($campaign->client_id)->firstOrFail();
        $gateway = GatewayProvider::findOrFail($gatewayIntegration->gateway_id);
        foreach ($audience_members as $audience_member) {
            $member = null;
            switch ($audience_member->entity_type) {
                case 'user':
                    $member = User::with('phone')->find($audience_member->entity_id);
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
                if (env('ENABLE_SMS', true)) {
                    if($member->phone){
                        FireTwilioMsg::dispatch($member->phone->value, $this->transform($markup, $member->toArray()));
                    }
                }
            }
        }

        $queued_sms_campaign = QueuedSmsCampaign::whereSmsCampaignId($sms_campaign_id)->first();
        if ($queued_sms_campaign) {
            $queued_sms_campaign->completed_at = Carbon::now();
            $queued_sms_campaign->save();
        }
    }

    protected function transform($string, $data)
    {
        foreach ($this->tokens as $token) {
            $string = str_replace("%{$token}%", $data[$token] ?? 'UNKNOWN_TOKEN', $string);
        }
        return $string;
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            $command->argument('sms_campaign_id')
        );
        if (env('ENABLE_SMS', true)) {
            $command->info('SMS Sent!');
        } else {
            $command->info('SMS Campaign SMS skipped because env.ENABLE_SMS is set to false');
        }
    }
}
