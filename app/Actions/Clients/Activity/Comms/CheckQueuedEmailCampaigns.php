<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Models\Comms\QueuedEmailCampaign;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckQueuedEmailCampaigns
{
    use AsAction;

    public string $commandSignature = 'email-campaigns:check';
    public string $commandDescription = 'Kicks off FireOffEmailCampaign actions for queued Email campaigns that are ready.';

    public function handle()
    {
        Log::debug("checking for queued email campaigns");
        $queued_campaigns = QueuedEmailCampaign::whereNull('started_at')->where('trigger_at', '<=', DB::raw('now()'))->get();
        foreach ($queued_campaigns as $campaign) {
            echo "Firing off emails for $campaign->id";
            FireOffEmailCampaign::dispatch($campaign->email_campaign_id);
            $campaign->started_at = new DateTime();
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
