<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Models\Comms\QueuedSmsCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckQueuedSmsCampaigns
{
    use AsAction;

    public string $commandSignature = 'sms-campaigns:check';
    public string $commandDescription = 'Kicks off FireOffSmsCampaign actions for queued SMS campaigns that are ready.';

    public function handle()
    {
        Log::info("checking for queued sms campaigns");
        $queued_campaigns = QueuedSmsCampaign::whereNull('started_at')->where('trigger_at', '<=', Carbon::now() )->get();
        foreach($queued_campaigns as $campaign){
            echo "Firing off SMS for $campaign->id";
            FireOffSmsCampaign::dispatch($campaign->sms_campaign_id);
            $campaign->started_at = Carbon::now();
            $campaign->save();
        }
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle();
        $command->info('Done');
    }
}
