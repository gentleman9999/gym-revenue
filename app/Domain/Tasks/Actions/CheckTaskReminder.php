<?php

declare(strict_types=1);

namespace App\Domain\Tasks\Actions;

use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Domain\Reminders\Actions\CreateReminder;
use App\Domain\Users\Models\User;
use App\Models\Tasks;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckTaskReminder
{
    use AsAction;

    public string $commandSignature = 'taskreminder:check';
    public string $commandDescription = 'Fires off task reminders that are ready.';

    public function handle(): void
    {
        Log::debug("checking for upcoming tasks");

        $date = now()->subMinutes(30)->format('Y-m-d H:i');

        $tasks = Tasks::where('due_at', 'like', '%' . $date . '%')->get();

        if (! empty($tasks)) {
            foreach ($tasks as $task) {
                $user = User::findOrFail($task->user_id);
                if (! empty($user)) {
                    CreateReminder::run([
                        'entity_type' => 'Task Reminder',
                        'entity_id' => Str::random(10),
                        'user_id' => $task->user_id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'remind_time' => 0,
                        'triggered_at' => now(),
                    ]);

                    CreateNotification::run([
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'entity_type' => Notification::TYPE_TASK_NOTIFICATION,
                    ], $user);
                }
            }
        }
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $command->info('Checking for Upcoming Tasks...');
        $this->handle();
        $command->info('Done Checking Tasks');
    }
}
