<?php

namespace App\Console;

use App\Actions\Clients\Activity\Comms\CheckQueuedEmailCampaigns;
use App\Actions\Clients\Activity\Comms\CheckQueuedSmsCampaigns;
use App\Actions\Simulation\GenerateRandomLeads;
use App\Actions\Simulation\GenerateRandomMembers;
use App\Domain\Reminders\Actions\CheckReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //Any Actions defined in App/Actions/**/* or App/Domain/*/Actions/*
        //with well defined signatures will get auto registered. Don't add them here.
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
        $schedule->job(new CheckQueuedEmailCampaigns())->everyMinute();
        $schedule->job(new CheckQueuedSmsCampaigns())->everyMinute();
        $schedule->job(new CheckReminders())->everyMinute();
        if (App::environment(['local', 'develop', 'staging'])) {
            $schedule->job(new GenerateRandomLeads())->everyFiveMinutes();
            $schedule->job(new GenerateRandomMembers())->everyTenMinutes();
        }
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
