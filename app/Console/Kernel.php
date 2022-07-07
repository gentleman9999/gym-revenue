<?php

namespace App\Console;

use App\Actions\Simulation\GenerateRandomLeads;
use App\Actions\Simulation\GenerateRandomMembers;
use App\Domain\Campaigns\ScheduledCampaigns\Actions\CheckScheduledCampaigns;
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
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CheckScheduledCampaigns())->everyMinute()->withoutOverlapping();
        $schedule->job(new CheckReminders())->everyMinute()->withoutOverlapping();
        if (App::environment(['local', 'develop', 'staging'])) {
            $schedule->job(new GenerateRandomLeads())->everyFiveMinutes()->withoutOverlapping();
            $schedule->job(new GenerateRandomMembers())->everyTenMinutes()->withoutOverlapping();
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
