<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Actions;

use App\Domain\Reminders\Reminder;
use App\Domain\Users\Aggregates\UserAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class TriggerReminder
{
    use AsAction;

    public string $commandSignature   = 'reminder:trigger {id}';
    public string $commandDescription = 'trigger a specific reminder';

    public function handle(Reminder $reminder): void
    {
        UserAggregate::retrieve($reminder->user_id)->triggerReminder($reminder->id)->persist();
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $command->info('Checking Reminders...');
        $this->handle($command->argument('id'));
        $command->info('Done Checking Reminders');
    }
}
