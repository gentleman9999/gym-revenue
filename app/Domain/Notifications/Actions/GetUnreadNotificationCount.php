<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUnreadNotificationCount
{
    use AsAction;

    public function handle(int $user_id): int
    {
        return Notification::where(['user_id' => $user_id])->count();
    }

    public function asController(ActionRequest $request): int
    {
        return $this->handle($request->user()->id);
    }
}
