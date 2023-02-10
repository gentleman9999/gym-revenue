<?php

declare(strict_types=1);

namespace App\Console;

use App\Domain\CalendarEvents\Actions\CheckOverdueTasks;
use App\Domain\Campaigns\ScheduledCampaigns\Actions\CheckScheduledCampaigns;
use App\Domain\Reminders\Actions\CheckReminders;
use App\Domain\Tasks\Actions\CheckTaskReminder;
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
        //Any Actions defined in App/Actions/**/* or App/Domain/*/Actions/*
        //with well defined signatures will get auto registered. Don't add them here.
    ];

    /**
     * Define the application's command schedule.
     *
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new CheckScheduledCampaigns())->everyMinute()->withoutOverlapping();
        $schedule->job(new CheckReminders())->everyMinute()->withoutOverlapping();
        $schedule->job(new CheckOverdueTasks())->everyMinute()->withoutOverlapping();
        $schedule->job(new CheckTaskReminder())->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
