<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeLaunched;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckScheduledCampaigns
{
    use AsAction;

    public string $commandSignature = 'scheduled-campaigns:check';
    public string $commandDescription = 'Checks scheduled campaigns that that are ready to launch';

    public function handle(): array
    {
        Log::debug("checking for scheduled campaigns that are ready to launch");
        $campaigns_to_launch = ScheduledCampaign::withoutGlobalScopes()->whereStatus(CampaignStatusEnum::PENDING)->where('send_at', '<=', CarbonImmutable::now())->get();
        $failure_to_launch = [];
        foreach ($campaigns_to_launch as $campaign) {
            try {
                LaunchScheduledCampaign::run($campaign);
            } catch (CampaignCouldNotBeLaunched $e) {
                $failure_to_launch[] = $campaign;
                report($e);
            }
        }

        $failed_ids = collect($failure_to_launch)->pluck('id');
        $launched_ids = $campaigns_to_launch->filter(function ($campaign) use ($failed_ids) {
            return ! $failed_ids->contains($campaign->id);
        })->pluck('id');

        return [
            'launched' => $launched_ids,
            'failed' => $failed_ids,
        ];
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $result = $this->handle();
        $num_launched = count($result['launched']);
        $num_failed = count($result['failed']);
        $command->info("Launched {$num_launched} scheduled campaigns.");
        if ($num_failed) {
            $command->info("{$num_failed} scheduled campaigns failed to launch.");
        }
    }
}
