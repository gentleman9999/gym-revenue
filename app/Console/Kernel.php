<?php

namespace App\Console;

use App\Actions\Clients\Activity\Comms\CheckQueuedSmsCampaigns;
use App\Actions\Clients\Activity\Comms\FireOffEmailCampaign;
use App\Actions\Clients\Activity\Comms\CheckQueuedEmailCampaigns;
use App\Actions\Clients\Activity\Comms\FireOffSmsCampaign;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        FireOffEmailCampaign::class,
        FireOffSmsCampaign::class,
        CheckQueuedEmailCampaigns::class,
        CheckQueuedSmsCampaigns::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //TODO: should be jobs, not commands
        $schedule->job(new CheckQueuedEmailCampaigns)->everyMinute();
        $schedule->job(new CheckQueuedSmsCampaigns)->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
