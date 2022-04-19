<?php

namespace App\Actions\Users\Reminders;

use App\Models\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckReminders
{
    use AsAction;

    public string $commandSignature = 'reminders:check';
    public string $commandDescription = 'Fires off reminders that are ready.';

    public function handle()
    {
        Log::debug("checking for reminders");
        $reminders = Reminder::whereNull('triggered_at')->get();
        //TODO:^^ need to figure out which ones are "ready" to be fired off.
        //could maybe do as a custom SQL where or just loop over and check for now.
        $reminders->each(function($reminder){
            //check if now <= NOW() - remind_time or something
            echo "Firing off reminders for $reminder->id";
            TriggerReminder::run($reminder->id);
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
