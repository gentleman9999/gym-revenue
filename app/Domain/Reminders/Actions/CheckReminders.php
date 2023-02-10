<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Reminders\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckReminders
{
    use AsAction;

    public string $commandSignature   = 'reminders:check';
    public string $commandDescription = 'Fires off reminders that are ready.';

    public function handle(): void
    {
        Log::debug("checking for reminders");
        $reminders = Reminder::whereNull('triggered_at')->with('event')->get();

        $reminders->each(function ($reminder): void {
            if ($reminder->entity_type == CalendarEvent::class) {
                $time = date('Y-m-d H:i:s', strtotime($reminder->event->start . '-' . $reminder->remind_time . ' minutes'));
                if ($time < date('Y-m-d H:i:s')) {
                    TriggerReminder::run($reminder);
                }
            }
        });
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $command->info('Checking Reminders...');
        $this->handle();
        $command->info('Done Checking Reminders');
    }
}
