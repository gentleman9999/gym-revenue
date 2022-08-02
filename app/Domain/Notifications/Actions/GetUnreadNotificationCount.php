<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUnreadNotificationCount
{
    use AsAction;

    public function handle(User $user): int
    {
        //TODO: is this efficient as possible? is it actually doing a db count or counting the results?
        return Notification::whereUserId($user->id)->count();
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        return true;
//    }

    public function asController(ActionRequest $request): int
    {
//        $data = $request->validated();
        return $this->handle(
            $request->user()
        );
//        return $notifications;
    }
}
