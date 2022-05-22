<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Features\SmsCampaignDetails;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignSMSTemplateToCampaign
{
    use AsAction;

    public function handle(string $template_id, string $campaign_id, string $created_by_user_id, string $client_id = null)
    {
        if (! is_null($client_id)) {
            ClientAggregate::retrieve($client_id)
                ->assignSmsTemplateToCampaign($template_id, $campaign_id, $created_by_user_id)
                ->persist();

        //$campaign = EmailCampaigns::find($campaign_id);
            //$template = EmailTemplates::find($template_id);
        } else {
            $detail = SmsCampaignDetails::create([
                'sms_campaign_id' => $campaign_id,
                'detail' => 'template_assigned',
                'value' => $template_id,
            ]);

            if ($created_by_user_id == 'auto') {
                $detail->misc = ['msg' => 'Template was auto-assigned', 'user' => $created_by_user_id];
            } else {
                $user = User::find($created_by_user_id);
                $detail->misc = ['msg' => 'Template was assigned by '.$user->name.' on '.date('Y-m-d'), 'user' => $created_by_user_id];
            }
            $detail->save();
        }
    }
}
