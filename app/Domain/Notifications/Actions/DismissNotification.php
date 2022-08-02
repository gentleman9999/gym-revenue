<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DismissNotification
{
    use AsAction;

    public string $commandSignature = 'notifications:dismiss {id}';

    public function handle(Notification $notification, User $user): void
    {
        UserAggregate::retrieve($user->id)->dismissNotification($notification->id)->persist();
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        return true;
//    }

    public function asController(ActionRequest $request, Notification $notification): int
    {
        $this->handle(
            $notification,
        );

        return GetUnreadNotificationCount::run($request->user());
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $notification = Notification::findOrFail($command->argument('id'));
        $this->handle(
            $notification,
            User::findOrFail($notification->user->id)
        );
        $command->info('Notification Dismissed');
    }
}
