<?php

namespace App\Domain\CalendarEvents\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEvents\CalendarEventAggregate;
use App\Domain\CalendarEventTypes\CalendarEventType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class CheckOverdueTasks
{
    use AsAction;

    public string $commandSignature = 'overduetasks:check';
    public string $commandDescription = 'Fires off reminders that are ready.';

    public function handle(): void
    {
        Log::debug("checking for overdue tasks");
        $taskIds = CalendarEventType::whereType('Task')->get();

        foreach ($taskIds as $taskId) {
            $tasks = CalendarEvent::whereEventTypeId($taskId->id)
                ->whereDate('start', '<', date('Y-m-d H:i:s'))
                ->whereOverdueReminderSent(false)
                ->whereNotNull('owner_id')
                ->get();

            //VarDumper::dump($tasks->toArray());

            foreach ($tasks as $task) {
                VarDumper::dump("Found task: ".$task->title);
                CalendarEventAggregate::retrieve($task->id)->notify($task->toArray())->persist();
            }
        }
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $command->info('Checking for Overdue Tasks...');
        $this->handle();
        $command->info('Done Checking Reminders');
    }
}
