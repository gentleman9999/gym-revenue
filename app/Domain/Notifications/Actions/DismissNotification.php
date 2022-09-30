<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Actions;

use App\Domain\Users\UserAggregate;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DismissNotification
{
    use AsAction;
    public string $commandSignature = 'notifications:dismiss {user_id} {id}';

    public function handle(string $user_id, string $notification_id): void
    {
        UserAggregate::retrieve($user_id)->dismissNotification($notification_id)->persist();
    }

    public function asController(ActionRequest $request, string $id): int
    {
        $user_id = $request->user()->id;
        $this->handle((string) $user_id, $id);

        return GetUnreadNotificationCount::run($user_id);
    }

    public function jsonResponse(int $unread_notification_count): JsonResponse
    {
        return new JsonResponse(['count' => $unread_notification_count]);
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle($command->argument('user_id'), $command->argument('id'));
        $command->info('Notification Dismissed');
    }
}
