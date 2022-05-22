<?php

namespace App\Actions\Users\Reminders;

use App\Aggregates\Users\UserAggregate;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class TriggerReminder
{
    use AsAction;

    public string $commandSignature = 'reminder:trigger {id}';
    public string $commandDescription = 'trigger a specific reminder';

    public function handle($id)
    {
        $reminder = Reminder::with('user')->findOrFail($id);
        UserAggregate::retrieve($reminder->user->id)->triggerReminder($id)->persist();
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $command->info('Checking Reminders...');
        $this->handle($command->argument('id'));
        $command->info('Done Checking Reminders');
    }
}
